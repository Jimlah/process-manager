<?php

namespace App\Livewire;

use App\Models\Command;
use App\Models\Project;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\On;
use Livewire\Component;
use Native\Desktop\Facades\ChildProcess;

class ProjectTree extends Component
{
    public Project $project;

    public ?int $selectedCommandId;

    public bool $expanded = false;

    public function mount(): void
    {
        if ($this->selectedCommandId) {
            $command = Command::find($this->selectedCommandId);
            if ($command && $command->project_id === $this->project->id) {
                $this->expanded = true;
            }
        }
    }

    public function toggle(): void
    {
        $this->expanded = ! $this->expanded;
    }

    public function selectCommand(int $commandId): void
    {
        $this->dispatch('command-selected', commandId: $commandId);
    }

    public function addCommand(): void
    {
        $this->dispatch('open-command-modal', projectId: $this->project->id);
    }

    public function editProject(): void
    {
        $this->dispatch('open-edit-project-modal', projectId: $this->project->id);
    }

    public function deleteProject(): void
    {
        $this->project->delete();
        $this->dispatch('project-deleted');
    }

    public function editCommand(int $commandId): void
    {
        $this->dispatch('open-edit-command-modal', commandId: $commandId);
    }

    public function deleteCommand(int $commandId): void
    {
        $command = $this->project->commands->find($commandId);

        if ($command) {
            $command->delete();
            $this->project->load('commands');
            $this->dispatch('command-updated');
        }
    }

    #[On('process-status-changed')]
    public function refreshCommands(): void
    {
        $this->project->load('commands');
    }

    public function startCommand(int $commandId): void
    {
        $command = $this->project->commands->find($commandId);

        if (! $command) {
            return;
        }

        $command->processLog?->update(['content' => '']);

        ChildProcess::start(
            cmd: $command->command,
            alias: $command->alias,
            cwd: $command->project->path,
            env: [],
        );

        $command->update(['status' => 'running']);

        $this->dispatch('process-status-changed');
    }

    public function stopCommand(int $commandId): void
    {
        $command = $this->project->commands->find($commandId);

        if (! $command) {
            return;
        }

        $command->update(['status' => 'stopped']);

        ChildProcess::stop($command->alias);

        $this->dispatch('process-status-changed');
    }

    public function getRunningCountProperty(): int
    {
        return $this->project->commands->where('status', 'running')->count();
    }

    public function render(): View
    {
        return view('livewire.project-tree');
    }
}
