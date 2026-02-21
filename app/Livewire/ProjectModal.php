<?php

namespace App\Livewire;

use App\Models\Project;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;
use Livewire\Component;

class ProjectModal extends Component
{
    public bool $show = false;

    public ?Project $project = null;

    #[Validate('required|string|max:255')]
    public string $name = '';

    #[Validate('required|string|max:500')]
    public string $path = '';

    public function mount(bool $show = false): void
    {
        $this->show = $show;
    }

    #[On('open-project-modal')]
    public function openForCreate(): void
    {
        $this->reset(['name', 'path', 'project']);
        $this->resetErrorBag();
        $this->show = true;
    }

    #[On('open-edit-project-modal')]
    public function openForEdit(int $projectId): void
    {
        $this->project = Project::findOrFail($projectId);
        $this->name = $this->project->name;
        $this->path = $this->project->path;
        $this->resetErrorBag();
        $this->show = true;
    }

    public function updatedShow(bool $value): void
    {
        if (! $value) {
            $this->dispatch('close-project-modal');
        }
    }

    public function save(): void
    {
        $this->validate();

        if ($this->project) {
            $this->project->update([
                'name' => $this->name,
                'path' => $this->path,
            ]);
        } else {
            Project::create([
                'name' => $this->name,
                'path' => $this->path,
            ]);
        }

        $this->reset(['name', 'path', 'project']);
        $this->dispatch('close-project-modal');
    }

    public function delete(): void
    {
        if ($this->project) {
            $this->project->delete();
        }

        $this->reset(['name', 'path', 'project']);
        $this->dispatch('close-project-modal');
    }

    public function cancel(): void
    {
        $this->reset(['name', 'path', 'project']);
        $this->dispatch('close-project-modal');
    }

    public function render(): View
    {
        return view('livewire.project-modal');
    }
}
