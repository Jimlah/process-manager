<?php

namespace App\Livewire;

use App\Models\Command;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\On;
use Livewire\Component;
use Native\Desktop\Events\ChildProcess\MessageReceived;
use Native\Desktop\Facades\ChildProcess;

class ProcessRunner extends Component
{
    public Command $command;

    public string $logs = '';

    #[On('native:'.MessageReceived::class)]
    public function onMessageReceived(string $data, string $alias): void
    {

        if ($alias !== $this->command->alias) {
            return;
        }

        $this->stream(
            to: "logs-{$this->command->id}",
            content: $data,
            replace: false
        );
    }

    #[On('native:child-process.process-exited')]
    public function onProcessExited(string $data): void
    {
        $data = json_decode($data, true);
        if ($data['alias'] === $this->command->alias) {
            $this->command->refresh();
            $this->command->update(['status' => 'stopped']);
        }
    }

    public function start(): void
    {
        ChildProcess::start(
            cmd: $this->command->command,
            alias: $this->command->alias,
            cwd: $this->command->project->path,
            persistent: true,
        );

        $this->command->update(['status' => 'running']);
    }

    public function stop(): void
    {
        ChildProcess::stop($this->command->alias);
        $this->command->update(['status' => 'stopped']);
    }

    public function clearLogs(): void
    {
        $this->logs = '';
    }

    public function render(): View
    {
        return view('livewire.process-runner');
    }
}
