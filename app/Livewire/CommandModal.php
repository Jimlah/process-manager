<?php

namespace App\Livewire;

use App\Models\Command;
use App\Models\Project;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;
use Livewire\Attributes\Validate;
use Livewire\Component;

class CommandModal extends Component
{
    public bool $show = false;

    public ?int $projectId = null;

    public ?Command $command = null;

    #[Validate('required|string|max:255')]
    public string $name = '';

    #[Validate('required|string|max:1000')]
    public string $commandText = '';

    public function mount(bool $show = false, ?int $projectId = null): void
    {
        $this->show = $show;
        $this->projectId = $projectId;
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

        if ($this->command) {
            $this->command->update([
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

        $this->reset(['name', 'commandText', 'command', 'projectId']);
        $this->dispatch('close-command-modal');
    }

    public function delete(): void
    {
        if ($this->command) {
            $this->command->delete();
        }

        $this->reset(['name', 'commandText', 'command', 'projectId']);
        $this->dispatch('close-command-modal');
    }

    public function cancel(): void
    {
        $this->reset(['name', 'commandText', 'command', 'projectId']);
        $this->dispatch('close-command-modal');
    }

    public function render(): View
    {
        return view('livewire.command-modal', [
            'project' => $this->projectId ? Project::find($this->projectId) : null,
        ]);
    }
}
