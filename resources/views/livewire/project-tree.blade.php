<div class="select-none mb-2">
    <!-- Project Header -->
    <div class="flex items-center justify-between group hover:bg-accent/50 transition-colors">
        <button wire:click="toggle" class="flex items-center gap-2 py-1.5 px-2 flex-1 text-left min-w-0">
            <x-icon name="chevron-down"
                class="w-3 h-3 text-muted-foreground transition-transform {{ $expanded ? 'rotate-0' : '-rotate-90' }}" />
            <x-icon name="folder" class="w-3.5 h-3.5 text-muted-foreground" />
            <span
                class="font-bold text-xs truncate uppercase tracking-wider text-foreground">{{ $project->name }}</span>

            @if($this->runningCount > 0)
                <x-badge class="ml-auto">{{ $this->runningCount }}</x-badge>
            @endif
        </button>

        <div class="opacity-0 group-hover:opacity-100 flex items-center gap-0.5 shrink-0 mr-1 transition-all">
            <x-tooltip text="Edit Project">
                <button wire:click.stop="editProject"
                    class="p-1 hover:bg-accent text-muted-foreground hover:text-foreground md:rounded transition-all">
                    <x-icon name="pencil" class="w-3.5 h-3.5" />
                </button>
            </x-tooltip>
            <x-tooltip text="Delete Project">
                <button wire:click.stop="deleteProject"
                    class="p-1 hover:bg-destructive/20 text-muted-foreground hover:text-destructive md:rounded transition-all"
                    wire:confirm="Are you sure you want to delete this project?">
                    <x-icon name="trash" class="w-3.5 h-3.5" />
                </button>
            </x-tooltip>
            <x-tooltip text="Add Command">
                <button wire:click.stop="addCommand"
                    class="p-1 hover:bg-accent text-muted-foreground hover:text-foreground md:rounded transition-all">
                    <x-icon name="plus" class="w-3.5 h-3.5" />
                </button>
            </x-tooltip>
        </div>
    </div>

    <!-- Commands List -->
    @if($expanded)
        <div class="ml-3 mt-0.5 space-y-px border-l border-border pl-1">
            @foreach($project->commands as $command)
                <div wire:key="cmd-{{ $command->id }}" class="flex items-center group hover:bg-accent hover:text-foreground">
                    <button wire:click="selectCommand({{ $command->id }})"
                        class=" flex items-center gap-2 py-1 px-2 text-xs text-left transition-all border-l-2 min-w-0
                                                            {{ $selectedCommandId === $command->id ? 'border-primary bg-primary/10 text-primary font-bold' : 'border-transparent text-muted-foreground ' }}">
                        <span class="text-muted-foreground opacity-50 select-none mr-1">|--</span>
                        <span class="truncate flex-1 font-mono">{{ $command->name }}</span>
                    </button>

                    <div class="flex items-center gap-1 mx-1">
                        <x-tooltip :text="'Auto-Start: ' . ($command->auto_start ? 'ON' : 'OFF')">
                            <button wire:click.stop="toggleAutoStart({{ $command->id }})"
                                class="p-0.5 rounded transition-all {{ $command->auto_start ? 'text-primary' : 'text-muted-foreground/30 hover:text-muted-foreground' }}">
                                <x-icon name="power" class="w-2.5 h-2.5" />
                            </button>
                        </x-tooltip>
                        <x-tooltip :text="'Auto-Restart: ' . ($command->auto_restart ? 'ON' : 'OFF')">
                            <button wire:click.stop="toggleAutoRestart({{ $command->id }})"
                                class="p-0.5 rounded transition-all {{ $command->auto_restart ? 'text-primary' : 'text-muted-foreground/30 hover:text-muted-foreground' }}">
                                <x-icon name="refresh" class="w-2.5 h-2.5" />
                            </button>
                        </x-tooltip>
                    </div>

                    @if($command->status === 'running')
                        <x-tooltip text="Stop process">
                            <button wire:click.stop="stopCommand({{ $command->id }})"
                                class="p-0.5 hover:bg-destructive/20 rounded transition-colors shrink-0">
                                <x-icon name="stop" class="w-3 h-3 text-success" />
                            </button>
                        </x-tooltip>
                    @else
                        <x-tooltip text="Start process">
                            <button wire:click.stop="startCommand({{ $command->id }})"
                                class="p-0.5 hover:bg-success/20 rounded transition-colors shrink-0">
                                <x-icon name="play" class="w-3 h-3 text-muted-foreground" />
                            </button>
                        </x-tooltip>
                    @endif
                    <div class="opacity-0 group-hover:opacity-100 flex items-center gap-0.5 shrink-0 ml-1 mr-1 transition-all">
                        <x-tooltip text="Edit Command">
                            <button wire:click.stop="editCommand({{ $command->id }})"
                                class="p-1 hover:bg-accent text-muted-foreground hover:text-foreground rounded transition-all">
                                <x-icon name="pencil" class="w-3 h-3" />
                            </button>
                        </x-tooltip>
                        <x-tooltip text="Delete Command">
                            <button wire:click.stop="deleteCommand({{ $command->id }})"
                                class="p-1 hover:bg-destructive/20 text-muted-foreground hover:text-destructive rounded transition-all"
                                wire:confirm="Are you sure you want to delete this command?">
                                <x-icon name="trash" class="w-3 h-3" />
                            </button>
                        </x-tooltip>
                    </div>
                </div>
            @endforeach

            @if($project->commands->isEmpty())
                <div class="text-xs text-muted-foreground py-1.5 px-6 italic font-mono flex items-center gap-2">
                    <span class="opacity-50 select-none">|--</span> No commands
                </div>
            @endif
        </div>
    @endif
</div>