<?php

namespace App\Livewire;

use App\Models\Command;
use App\Models\Project;
use Illuminate\Contracts\View\View;
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

    public function getRunningCountProperty(): int
    {
        return $this->project->commands->where('status', 'running')->count();
    }

    public function render(): View
    {
        return view('livewire.project-tree');
    }
}
