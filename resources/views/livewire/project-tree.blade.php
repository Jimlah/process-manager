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
            <button wire:click="editProject"
                class="p-1 hover:bg-accent text-muted-foreground hover:text-foreground transition-all"
                title="Edit Project">
                <x-icon name="pencil" class="w-3.5 h-3.5" />
            </button>
            <button wire:click="addCommand"
                class="p-1 hover:bg-accent text-muted-foreground hover:text-foreground transition-all"
                title="Add Command">
                <x-icon name="plus" class="w-3.5 h-3.5" />
            </button>
        </div>
    </div>

    <!-- Commands List -->
    @if($expanded)
        <div class="ml-3 mt-0.5 space-y-px border-l border-border pl-1">
            @foreach($project->commands as $command)
                <div wire:key="cmd-{{ $command->id }}" class="flex items-center group/cmd">
                    <button wire:click="selectCommand({{ $command->id }})"
                        class="flex-1 flex items-center gap-2 py-1 px-2 text-xs text-left transition-all border-l-2 min-w-0
                                               {{ $selectedCommandId === $command->id
                    ? 'border-primary bg-primary/10 text-primary font-bold' : 'border-transparent text-muted-foreground hover:bg-accent hover:text-foreground' }}">
                        <span class="text-muted-foreground opacity-50 select-none mr-1">|--</span>
                        @if($command->status === 'running')
                            <button wire:click.stop="stopCommand({{ $command->id }})"
                                class="p-0.5 hover:bg-destructive/20 rounded transition-colors shrink-0" title="Stop process">
                                <x-icon name="stop" class="w-3 h-3 text-success" />
                            </button>
                        @else
                            <button wire:click.stop="startCommand({{ $command->id }})"
                                class="p-0.5 hover:bg-success/20 rounded transition-colors shrink-0" title="Start process">
                                <x-icon name="play" class="w-3 h-3 text-muted-foreground" />
                            </button>
                        @endif
                        <span class="truncate flex-1 font-mono">{{ $command->name }}</span>
                    </button>
                    <button wire:click.stop="editCommand({{ $command->id }})"
                        class="opacity-0 group-hover/cmd:opacity-100 p-1 hover:bg-accent text-muted-foreground hover:text-foreground transition-all shrink-0 mr-1"
                        title="Edit Command">
                        <x-icon name="pencil" class="w-3 h-3" />
                    </button>
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