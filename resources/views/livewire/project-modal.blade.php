<div>
    <x-modal :show="$show" :title="$project ? 'EDIT PROJECT' : 'NEW PROJECT'" wire:click.self="$set('show', false)">
        <x-slot:close>
            <button type="button" class="text-muted-foreground hover:text-foreground transition-colors"
                wire:click="$set('show', false)">
                <x-icon name="plus" class="w-4 h-4 rotate-45" />
            </button>
        </x-slot:close>

        <div class="space-y-4">
            <div>
                <label class="block text-xs font-bold text-muted-foreground mb-1 uppercase tracking-wider">Project
                    Name</label>
                <x-input wire:model="name" placeholder="e.g., eClinic" />
                @error('name')<span class="text-destructive text-xs mt-1 block">{{ $message }}</span>@enderror
            </div>

            <div>
                <label class="block text-xs font-bold text-muted-foreground mb-1 uppercase tracking-wider">Project
                    Path</label>
                <x-input wire:model="path" placeholder="/Users/{{ get_current_user() }}/Projects/eclinic" />
                @error('path')<span class="text-destructive text-xs mt-1 block">{{ $message }}</span>@enderror
            </div>
        </div>

        <div class="flex items-center justify-between mt-6 pt-4 border-t border-border">
            <div>
                @if($project)
                    <x-button variant="destructive" wire:click="delete">Delete</x-button>
                @endif
            </div>
            <div class="flex items-center gap-3">
                <x-button variant="ghost" wire:click="$set('show', false)">Cancel</x-button>
                <x-button wire:click="save">{{ $project ? 'Update' : 'Create' }}</x-button>
            </div>
        </div>
    </x-modal>
</div>