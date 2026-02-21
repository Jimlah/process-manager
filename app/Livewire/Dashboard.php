<?php

namespace App\Livewire;

use App\Models\Command;
use App\Models\Project;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\On;
use Livewire\Component;
use Native\Desktop\Dialog;

class Dashboard extends Component
{
    public ?int $selectedCommandId = null;

    public bool $showProjectModal = false;

    public bool $showCommandModal = false;

    public ?int $commandModalProjectId = null;

    // Project form fields
    public string $projectName = '';

    public string $projectPath = '';

    // Command form fields
    public string $commandName = '';

    public string $commandText = '';

    #[On('command-selected')]
    public function selectCommand(int $commandId): void
    {
        $this->selectedCommandId = $commandId;
    }

    public function openProjectModal(): void
    {
        $this->projectName = '';
        $this->projectPath = '';
        $this->resetErrorBag();
        $this->showProjectModal = true;
    }

    public function selectProjectPath(): void
    {
        $path = Dialog::new()
            ->folders()
            ->open();

        if ($path) {
            $this->projectPath = $path;
        }
    }

    public function closeProjectModal(): void
    {
        $this->showProjectModal = false;
        $this->projectName = '';
        $this->projectPath = '';
        $this->resetErrorBag();
    }

    public function saveProject(): void
    {
        $this->validate([
            'projectName' => 'required|string|max:255',
            'projectPath' => 'required|string|max:500',
        ]);

        Project::create([
            'name' => $this->projectName,
            'path' => $this->projectPath,
        ]);

        $this->closeProjectModal();
    }

    #[On('open-command-modal')]
    public function openCommandModal(int $projectId): void
    {
        $this->commandModalProjectId = $projectId;
        $this->commandName = '';
        $this->commandText = '';
        $this->resetErrorBag();
        $this->showCommandModal = true;
    }

    public function closeCommandModal(): void
    {
        $this->showCommandModal = false;
        $this->commandModalProjectId = null;
        $this->commandName = '';
        $this->commandText = '';
        $this->resetErrorBag();
    }

    public function saveCommand(): void
    {
        $this->validate([
            'commandName' => 'required|string|max:255',
            'commandText' => 'required|string|max:1000',
        ]);

        $alias = \Illuminate\Support\Str::slug($this->commandName).'-'.uniqid();

        Command::create([
            'project_id' => $this->commandModalProjectId,
            'name' => $this->commandName,
            'command' => $this->commandText,
            'alias' => $alias,
            'status' => 'stopped',
        ]);

        $this->closeCommandModal();
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
        return view('livewire.dashboard', [
            'projects' => Project::with('commands')->orderBy('name')->get(),
        ]);
    }
}
