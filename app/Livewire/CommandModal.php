<?php

namespace App\Livewire;

use App\Models\Command;
use App\Models\Project;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;
use Livewire\Component;

class CommandModal extends Component
{
    public bool $show = false;

    public ?int $projectId = null;

    public ?int $editingCommandId = null;

    #[Validate('required|string|max:255')]
    public string $name = '';

    #[Validate('required|string|max:1000')]
    public string $commandText = '';

    public function mount(bool $show = false, ?int $projectId = null): void
    {
        $this->show = $show;
        $this->projectId = $projectId;
    }

    #[On('open-command-modal')]
    public function openForCreate(int $projectId): void
    {
        $this->reset(['name', 'commandText', 'editingCommandId']);
        $this->projectId = $projectId;
        $this->resetErrorBag();
        $this->show = true;
    }

    #[On('open-edit-command-modal')]
    public function openForEdit(int $commandId): void
    {
        $command = Command::findOrFail($commandId);
        $this->editingCommandId = $command->id;
        $this->projectId = $command->project_id;
        $this->name = $command->name;
        $this->commandText = $command->command;
        $this->resetErrorBag();
        $this->show = true;
    }

    public function updatedShow(bool $value): void
    {
        if (! $value) {
            $this->dispatch('close-command-modal');
        }
    }

    public function save(): void
    {
        $this->validate();

        if ($this->editingCommandId) {
            Command::findOrFail($this->editingCommandId)->update([
                'name' => $this->name,
                'command' => $this->commandText,
            ]);
        } else {
            $alias = Str::slug($this->name).'-'.uniqid();

            Command::create([
                'project_id' => $this->projectId,
                'name' => $this->name,
                'command' => $this->commandText,
                'alias' => $alias,
                'status' => 'stopped',
            ]);
        }

        $this->reset(['name', 'commandText', 'editingCommandId', 'projectId']);
        $this->show = false;
        $this->dispatch('close-command-modal');
    }

    public function delete(): void
    {
        if ($this->editingCommandId) {
            Command::findOrFail($this->editingCommandId)->delete();
        }

        $this->reset(['name', 'commandText', 'editingCommandId', 'projectId']);
        $this->show = false;
        $this->dispatch('close-command-modal');
    }

    public function cancel(): void
    {
        $this->reset(['name', 'commandText', 'editingCommandId', 'projectId']);
        $this->show = false;
        $this->dispatch('close-command-modal');
    }

    public function render(): View
    {
        return view('livewire.command-modal', [
            'project' => $this->projectId ? Project::find($this->projectId) : null,
        ]);
    }
}
