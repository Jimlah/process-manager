<div>
    <x-modal :show="$show" :title="$editingCommandId ? 'EDIT COMMAND' : 'NEW COMMAND'"
        wire:click.self="$set('show', false)">
        <x-slot:close>
            <button type="button" class="text-muted-foreground hover:text-foreground transition-colors"
                wire:click="$set('show', false)">
                <x-icon name="plus" class="w-4 h-4 rotate-45" />
            </button>
        </x-slot:close>

        @if($project)
            <p class="text-xs text-muted-foreground mb-4">
                Target: <span class="text-primary">{{ $project->name }}</span>
            </p>
        @endif

        <div class="space-y-4">
            <div>
                <label class="block text-xs font-bold text-muted-foreground mb-1 uppercase tracking-wider">Command
                    Name</label>
                <x-input wire:model="name" placeholder="e.g., Vite Dev Server" />
                @error('name')<span class="text-destructive text-xs mt-1 block">{{ $message }}</span>@enderror
            </div>

            <div>
                <label
                    class="block text-xs font-bold text-muted-foreground mb-1 uppercase tracking-wider">Command</label>
                <textarea wire:model="commandText" rows="3"
                    class="flex w-full bg-background px-3 py-2 text-sm shadow-sm transition-colors placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring border border-input resize-none"
                    placeholder="npm run dev"></textarea>
                @error('commandText')<span class="text-destructive text-xs mt-1 block">{{ $message }}</span>@enderror
            </div>

            <div class="grid grid-cols-2 gap-4 pt-2">
                <label class="flex items-center gap-2 cursor-pointer group">
                    <input type="checkbox" wire:model="autoStart"
                        class="w-4 h-4 rounded border-input bg-background text-primary focus:ring-primary">
                    <div class="flex flex-col">
                        <span
                            class="text-xs font-bold uppercase tracking-wider text-muted-foreground group-hover:text-foreground transition-colors">Auto-Start</span>
                        <span class="text-[10px] text-muted-foreground/60">Run on startup</span>
                    </div>
                </label>

                <label class="flex items-center gap-2 cursor-pointer group">
                    <input type="checkbox" wire:model="autoRestart"
                        class="w-4 h-4 rounded border-input bg-background text-primary focus:ring-primary">
                    <div class="flex flex-col">
                        <span
                            class="text-xs font-bold uppercase tracking-wider text-muted-foreground group-hover:text-foreground transition-colors">Auto-Restart</span>
                        <span class="text-[10px] text-muted-foreground/60">Restart if exit</span>
                    </div>
                </label>
            </div>
        </div>

        <div class="flex items-center justify-between mt-6 pt-4 border-t border-border">
            <div>
                @if($editingCommandId)
                    <x-button variant="destructive" wire:click="delete">Delete</x-button>
                @endif
            </div>
            <div class="flex items-center gap-3">
                <x-button variant="ghost" wire:click="$set('show', false)">Cancel</x-button>
                <x-button wire:click="save">{{ $editingCommandId ? 'Update' : 'Create' }}</x-button>
            </div>
        </div>
    </x-modal>
</div>