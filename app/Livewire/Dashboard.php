<?php

namespace App\Livewire;

use App\Models\Command;
use App\Models\Project;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

class Dashboard extends Component
{
    public ?int $selectedCommandId = null;

    public bool $projectsLoaded = false;

    #[On('command-selected')]
    public function selectCommand(int $commandId): void
    {
        $this->selectedCommandId = $commandId;
    }

    public function openProjectModal(): void
    {
        $this->dispatch('open-project-modal');
    }

    #[On('close-project-modal')]
    #[On('close-command-modal')]
    public function refreshData(): void
    {
        // Triggers re-render so computed properties update
    }

    public function loadProjects(): void
    {
        $this->projectsLoaded = true;
    }

    #[On('process-status-changed')]
    public function handleStatusChange(): void
    {
        // Triggers re-render so computed properties update
    }

    #[Computed]
    public function projects(): Collection
    {
        if (! $this->projectsLoaded) {
            return new Collection;
        }

        return Project::with('commands')->orderBy('name')->get();
    }

    public function getRunningCommandsProperty(): Collection
    {
        return Command::where('status', 'running')->with('project')->get();
    }

    public function getSelectedCommandProperty(): ?Command
    {
        if (! $this->selectedCommandId) {
            return null;
        }

        return Command::with('project')->find($this->selectedCommandId);
    }

    public function render(): View
    {
        return view('livewire.dashboard');
    }
}
