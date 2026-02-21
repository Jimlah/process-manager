<?php

namespace App\Livewire;

use App\Events\CommandStartRequested;
use App\Events\CommandStopRequested;
use App\Events\ProcessStatusChanged;
use App\Models\Command;
use App\Models\Project;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\On;
use Livewire\Component;

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

    #[On('native:'.ProcessStatusChanged::class)]
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

        CommandStartRequested::dispatch($command);
    }

    public function stopCommand(int $commandId): void
    {
        $command = $this->project->commands->find($commandId);

        if (! $command) {
            return;
        }

        CommandStopRequested::dispatch($command);
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
