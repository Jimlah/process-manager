<?php

namespace App\Livewire;

use App\Events\ProcessStatusChanged;
use App\Models\Command;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\On;
use Livewire\Component;
use Native\Desktop\Events\ChildProcess\MessageReceived;
use Native\Desktop\Events\ChildProcess\ProcessExited;

class ProcessRunner extends Component
{
    public Command $command;

    public string $logs = '';

    public function mount(): void
    {
        $this->logs = $this->convertAnsiToHtml(
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

        $data = str_ends_with($data, "\n") ? $data : $data."\n";

        $html = $this->convertAnsiToHtml($data);

        $this->logs .= $html;

        $this->stream(
            content: $html,
            replace: false,
            el: "logs-{$this->command->id}",
        );
    }

    private function convertAnsiToHtml(string $text): string
    {
        $converter = new \SensioLabs\AnsiConverter\AnsiToHtmlConverter;

        return $converter->convert($text);
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
        $this->logs = '';
        \App\Events\CommandStartRequested::dispatch($this->command);
        $this->command->refresh();
    }

    public function stop(): void
    {
        $this->logs = '';
        \App\Events\CommandStopRequested::dispatch($this->command);
        $this->command->refresh();
    }

    #[On('native:'.ProcessStatusChanged::class)]
    public function onProcessStatusChanged(array $command): void
    {
        if ($command['id'] === $this->command->id) {
            $this->command->refresh();
        }
    }

    #[On('process-status-changed')]
    public function refreshCommand(): void
    {
        $this->command->refresh();
    }

    public function clearLogs(): void
    {
        $this->logs = '';
        $this->command->processLog?->update(['content' => '']);
    }

    public function toggleAutoStart(): void
    {
        $this->command->update(['auto_start' => ! $this->command->auto_start]);
        \App\Events\ProcessStatusChanged::dispatch($this->command);
    }

    public function toggleAutoRestart(): void
    {
        $this->command->update(['auto_restart' => ! $this->command->auto_restart]);
        \App\Events\ProcessStatusChanged::dispatch($this->command);
    }

    public function render(): View
    {
        return view('livewire.process-runner');
    }
}
