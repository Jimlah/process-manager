<div class="flex h-full w-full overflow-hidden" x-data="{}">
    <!-- Sidebar: Tree Navigation -->
    <aside class="w-72 bg-card border-r border-border flex flex-col shrink-0 overflow-hidden">
        <div class="p-4 border-b border-border flex items-center justify-between">
            <h2 class="font-bold text-foreground tracking-widest text-sm flex items-center gap-2">
                <x-icon name="terminal" class="w-4 h-4 text-primary" />
                PROJECTS
            </h2>
            <button wire:click="openProjectModal" class="text-muted-foreground hover:text-primary transition-colors">
                <x-icon name="plus" class="w-4 h-4" />
            </button>
        </div>

        <div class="flex-1 overflow-y-auto p-2 space-y-1">
            @foreach($projects as $project)
                <livewire:project-tree
                    :project="$project"
                    :selected-command-id="$selectedCommandId"
                    :key="'project-'.$project->id"
                />
            @endforeach
            @if($projects->isEmpty())
                <div class="text-xs text-muted-foreground p-4 text-center">No projects configured.</div>
            @endif
        </div>

        <!-- Theme Toggle -->
        <div class="border-t border-border p-3" x-data="themeToggle()" x-init="init()">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-muted-foreground uppercase tracking-wider">THEME</span>
                <div class="flex items-center gap-0.5 border border-border bg-background p-0.5">
                    <button
                        @click="set('light')"
                        :class="theme === 'light' ? 'bg-primary text-primary-foreground' : 'text-muted-foreground hover:text-foreground'"
                        class="p-1.5 transition-colors"
                        title="Light Mode"
                    >
                        <x-icon name="sun" class="w-3.5 h-3.5" />
                    </button>
                    <button
                        @click="set('dark')"
                        :class="theme === 'dark' ? 'bg-primary text-primary-foreground' : 'text-muted-foreground hover:text-foreground'"
                        class="p-1.5 transition-colors"
                        title="Dark Mode"
                    >
                        <x-icon name="moon" class="w-3.5 h-3.5" />
                    </button>
                    <button
                        @click="set('system')"
                        :class="theme === 'system' ? 'bg-primary text-primary-foreground' : 'text-muted-foreground hover:text-foreground'"
                        class="p-1.5 transition-colors"
                        title="System"
                    >
                        <x-icon name="monitor" class="w-3.5 h-3.5" />
                    </button>
                </div>
            </div>
        </div>
    </aside>

    <!-- Main Content: Log Viewer -->
    <main class="flex-1 bg-background overflow-hidden flex flex-col">
        @if($this->selectedCommand)
            <livewire:process-runner
                :command="$this->selectedCommand"
                :key="'runner-'.$selectedCommandId"
            />
        @else
            <div class="flex-1 flex items-center justify-center text-muted-foreground">
                <div class="text-center flex flex-col items-center">
                    <x-icon name="terminal" class="w-16 h-16 opacity-20 mb-4" />
                    <p class="text-lg font-bold text-foreground/50">NO PROCESS SELECTED</p>
                    <p class="text-sm mt-2">Select a command from the sidebar to view output</p>
                </div>
            </div>
        @endif
    </main>

    <!-- Project Modal -->
    <x-modal :show="$showProjectModal" title="NEW PROJECT" wire:click.self="closeProjectModal">
        <x-slot:close>
            <button type="button" class="text-muted-foreground hover:text-foreground transition-colors" wire:click="closeProjectModal">
                <x-icon name="plus" class="w-4 h-4 rotate-45" />
            </button>
        </x-slot:close>

        <div class="space-y-4">
            <div>
                <label class="block text-xs font-bold text-muted-foreground mb-1 uppercase tracking-wider">Project Name</label>
                <x-input wire:model="projectName" placeholder="e.g., eClinic" />
                @error('projectName')<span class="text-destructive text-xs mt-1 block">{{ $message }}</span>@enderror
            </div>

            <div>
                <label class="block text-xs font-bold text-muted-foreground mb-1 uppercase tracking-wider">Project Path</label>
                <div class="flex gap-2">
                    <x-input wire:model="projectPath" placeholder="/Users/{{ get_current_user() }}/Projects/eclinic" />
                    <x-button variant="outline" wire:click="selectProjectPath">Browse</x-button>
                </div>
                @error('projectPath')<span class="text-destructive text-xs mt-1 block">{{ $message }}</span>@enderror
            </div>
        </div>

        <div class="flex items-center justify-end gap-3 mt-6 pt-4 border-t border-border">
            <x-button variant="ghost" wire:click="closeProjectModal">Cancel</x-button>
            <x-button wire:click="saveProject">Create Project</x-button>
        </div>
    </x-modal>

    <!-- Command Modal -->
    <x-modal :show="$showCommandModal" title="NEW COMMAND" wire:click.self="closeCommandModal">
        <x-slot:close>
            <button type="button" class="text-muted-foreground hover:text-foreground transition-colors" wire:click="closeCommandModal">
                <x-icon name="plus" class="w-4 h-4 rotate-45" />
            </button>
        </x-slot:close>

        @if($commandModalProjectId)
            <p class="text-xs text-muted-foreground mb-4">
                Target: <span class="text-primary">{{ App\Models\Project::find($commandModalProjectId)?->name }}</span>
            </p>
        @endif

        <div class="space-y-4">
            <div>
                <label class="block text-xs font-bold text-muted-foreground mb-1 uppercase tracking-wider">Command Name</label>
                <x-input wire:model="commandName" placeholder="e.g., Vite Dev Server" />
                @error('commandName')<span class="text-destructive text-xs mt-1 block">{{ $message }}</span>@enderror
            </div>

            <div>
                <label class="block text-xs font-bold text-muted-foreground mb-1 uppercase tracking-wider">Command</label>
                <textarea
                    wire:model="commandText"
                    rows="3"
                    class="flex w-full bg-background px-3 py-2 text-sm shadow-sm transition-colors placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring border border-input resize-none"
                    placeholder="npm run dev"
                ></textarea>
                @error('commandText')<span class="text-destructive text-xs mt-1 block">{{ $message }}</span>@enderror
            </div>
        </div>

        <div class="flex items-center justify-end gap-3 mt-6 pt-4 border-t border-border">
            <x-button variant="ghost" wire:click="closeCommandModal">Cancel</x-button>
            <x-button wire:click="saveCommand">Create Command</x-button>
        </div>
    </x-modal>
</div>