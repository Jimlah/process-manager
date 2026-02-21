<?php

namespace App\Livewire;

use App\Models\Command;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\On;
use Livewire\Component;
use Native\Desktop\Events\ChildProcess\MessageReceived;
use Native\Desktop\Events\ChildProcess\ProcessExited;
use Native\Desktop\Facades\ChildProcess;
use Native\Desktop\Facades\Notification;

class ProcessRunner extends Component
{
    public Command $command;

    public string $logs = '';

    public function mount(): void
    {
        $this->logs = $this->stripAnsiCodes(
            $this->command->processLog?->content ?? ''
        );
    }

    #[On('native:'.MessageReceived::class)]
    public function onMessageReceived(string $data, string $alias): void
    {
        if ($alias !== $this->command->alias) {
            return;
        }

        if ($this->command->status !== 'running') {
            return;
        }

        $clean = $this->stripAnsiCodes($data);

        $this->logs .= $clean;

        $this->stream(
            content: $clean,
            replace: false,
            el: "logs-{$this->command->id}",
        );
    }

    private function stripAnsiCodes(string $text): string
    {
        // Strip CSI sequences (colors, cursor, erase) and OSC sequences (terminal titles)
        return preg_replace([
            '/\e\[[0-9;]*[A-Za-z]/',
            '/\e\][^\a]*(?:\a|\e\\\\)/',
        ], '', $text);
    }

    #[On('native:'.ProcessExited::class)]
    public function onProcessExited(string $alias, int $code): void
    {
        if ($alias !== $this->command->alias) {
            return;
        }

        $this->command->refresh();
        $this->command->update(['status' => 'stopped']);

        $this->dispatch('process-status-changed');
    }

    public function start(): void
    {
        $this->command->processLog?->update(['content' => '']);
        $this->logs = '';

        ChildProcess::start(
            cmd: $this->command->command,
            alias: $this->command->alias,
            cwd: $this->command->project->path,
            env: [],
        );

        $this->command->update(['status' => 'running']);

        $this->dispatch('process-status-changed');

        Notification::title('Process Started')
            ->message("Started '{$this->command->name}' in {$this->command->project->name}.")
            ->show();
    }

    public function stop(): void
    {
        $this->command->update(['status' => 'stopped']);

        $this->dispatch('process-status-changed');

        $alias = $this->command->alias;

        ChildProcess::stop($alias);

        $this->clearLogs();

        // The process may have been restarted by the persistent watchdog
        // before the stop command could disable it. Retry to ensure it's dead.
        usleep(500_000);

        if (ChildProcess::get($alias) !== null) {
            ChildProcess::stop($alias);
        }

        Notification::title('Process Stopped')
            ->message("Stopped '{$this->command->name}'.")
            ->show();
    }

    public function clearLogs(): void
    {
        $this->logs = '';
        $this->command->processLog?->update(['content' => '']);
    }

    public function render(): View
    {
        return view('livewire.process-runner');
    }
}
