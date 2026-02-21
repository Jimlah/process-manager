<div class="flex flex-col h-full bg-background relative">
    <!-- Header (Tmux pane title) -->
    <div class="bg-card border-b border-border flex items-center justify-between shrink-0 p-2 pl-4">
        <div class="flex items-center gap-2 min-w-0">
            <span class="text-primary font-bold uppercase tracking-wider text-xs">{{ $command->project->name }}</span>
            <span class="text-muted-foreground">/</span>
            <span class="text-foreground font-mono text-sm truncate">{{ $command->name }}</span>
            <span class="text-muted-foreground px-2">|</span>
            <code class="font-mono text-xs text-muted-foreground truncate max-w-sm">{{ $command->command }}</code>
        </div>

        <div class="flex items-center gap-2 shrink-0">
            @if($command->status === 'running')
                <x-badge variant="success" class="animate-pulse flex items-center gap-1.5 px-2">
                    <span class="w-1.5 h-1.5 rounded-full bg-success"></span>
                    RUNNING
                </x-badge>
                <x-tooltip text="Stop Process">
                    <x-button variant="destructive" size="sm" wire:click="stop" wire:loading.attr="disabled"
                        class="gap-1.5 px-3">
                        <x-icon name="stop" class="w-3.5 h-3.5" />
                        STOP
                    </x-button>
                </x-tooltip>
            @else
                <x-badge variant="secondary" class="flex items-center gap-1.5 px-2">
                    <span class="w-1.5 h-1.5 rounded-full bg-muted-foreground"></span>
                    STOPPED
                </x-badge>
                <x-tooltip text="Start Process">
                    <x-button variant="success" size="sm" wire:click="start" wire:loading.attr="disabled"
                        class="gap-1.5 px-3">
                        <x-icon name="play" class="w-3.5 h-3.5" />
                        START
                    </x-button>
                </x-tooltip>
            @endif

            <div class="w-px h-6 bg-border mx-1"></div>

            <x-tooltip text="Run automatically on startup">
                <x-button variant="outline" size="sm" wire:click="toggleAutoStart"
                    class="gap-1.5 px-3 {{ $command->auto_start ? 'text-primary bg-primary/5 border-primary/30' : 'text-muted-foreground' }}">
                    <x-icon name="power" class="w-3.5 h-3.5" />
                    <span class="text-[10px] uppercase font-bold tracking-widest hidden sm:inline">Auto-Start</span>
                </x-button>
            </x-tooltip>

            <x-tooltip text="Automatically restart if process exits">
                <x-button variant="outline" size="sm" wire:click="toggleAutoRestart"
                    class="gap-1.5 px-3 {{ $command->auto_restart ? 'text-primary bg-primary/5 border-primary/30' : 'text-muted-foreground' }}">
                    <x-icon name="refresh" class="w-3.5 h-3.5" />
                    <span class="text-[10px] uppercase font-bold tracking-widest hidden sm:inline">Auto-Restart</span>
                </x-button>
            </x-tooltip>

            <div class="w-px h-6 bg-border mx-1"></div>

            <x-tooltip text="Clear terminal logs">
                <x-button variant="outline" size="sm" wire:click="clearLogs"
                    class="gap-1.5 px-3 uppercase text-[10px] font-bold tracking-widest">
                    <x-icon name="trash" class="w-3.5 h-3.5" />
                    CLEAR
                </x-button>
            </x-tooltip>
        </div>
    </div>

    <!-- Terminal -->
    <div class="flex-1 bg-terminal overflow-hidden flex flex-col relative">
        <div class="flex-1 overflow-y-auto p-4 font-mono text-sm text-terminal-text whitespace-pre-wrap leading-relaxed focus:outline-none"
            id="terminal-{{ $command->id }}" wire:stream="logs-{{ $command->id }}">
            {!! $logs !!}
        </div>
    </div>
</div>