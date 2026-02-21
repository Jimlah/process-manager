<div class="flex h-full w-full overflow-hidden" x-data="{}" wire:init="loadProjects">
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
            @if(!$projectsLoaded)
                {{-- Skeleton placeholders --}}
                @for($i = 0; $i < 3; $i++)
                    <div class="animate-pulse mb-2">
                        <div class="flex items-center gap-2 py-1.5 px-2">
                            <div class="w-3 h-3 bg-muted-foreground/20 rounded"></div>
                            <div class="w-3.5 h-3.5 bg-muted-foreground/20 rounded"></div>
                            <div class="h-3 bg-muted-foreground/20 rounded flex-1"></div>
                        </div>
                    </div>
                @endfor
            @else
                @foreach($this->projects as $project)
                    <livewire:project-tree :project="$project" :selected-command-id="$selectedCommandId"
                        :key="'project-' . $project->id" />
                @endforeach
                @if($this->projects->isEmpty())
                    <div class="text-xs text-muted-foreground p-4 text-center">No projects configured.</div>
                @endif
            @endif
        </div>

        <!-- Theme Toggle & Settings -->
        <div class="border-t border-border p-3 space-y-2">
            <div x-data="themeToggle()" x-init="init()">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-muted-foreground uppercase tracking-wider">THEME</span>
                    <div class="flex items-center gap-0.5 border border-border bg-background p-0.5">
                        <button @click="set('light')"
                            :class="theme === 'light' ? 'bg-primary text-primary-foreground' : 'text-muted-foreground hover:text-foreground'"
                            class="p-1.5 transition-colors" title="Light Mode">
                            <x-icon name="sun" class="w-3.5 h-3.5" />
                        </button>
                        <button @click="set('dark')"
                            :class="theme === 'dark' ? 'bg-primary text-primary-foreground' : 'text-muted-foreground hover:text-foreground'"
                            class="p-1.5 transition-colors" title="Dark Mode">
                            <x-icon name="moon" class="w-3.5 h-3.5" />
                        </button>
                        <button @click="set('system')"
                            :class="theme === 'system' ? 'bg-primary text-primary-foreground' : 'text-muted-foreground hover:text-foreground'"
                            class="p-1.5 transition-colors" title="System">
                            <x-icon name="monitor" class="w-3.5 h-3.5" />
                        </button>
                    </div>
                </div>
            </div>

            <a href="/settings"
                class="flex items-center justify-between py-2 text-xs font-bold text-muted-foreground uppercase tracking-wider hover:text-primary transition-colors cursor-pointer">
                <span>Settings</span>
                <x-icon name="settings" class="w-3.5 h-3.5" />
            </a>
        </div>
    </aside>

    <!-- Main Content: Log Viewer -->
    <main class="flex-1 bg-background overflow-hidden flex flex-col">
        @if($this->selectedCommand)
            <livewire:process-runner :command="$this->selectedCommand" :key="'runner-' . $selectedCommandId" />
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
    <livewire:project-modal />

    <!-- Command Modal -->
    <livewire:command-modal />
</div>