<div class="select-none">
    <!-- Project Header -->
    <div class="flex items-center justify-between group rounded hover:bg-gray-700/50 transition-colors">
        <button
            wire:click="toggle"
            class="flex items-center gap-2 py-2 px-2 flex-1 text-left min-w-0"
        >
            <span class="text-gray-400 text-xs w-4 transition-transform {{ $expanded ? 'rotate-0' : '-rotate-90' }}">▼</span>
            <span class="font-medium text-sm truncate">{{ $project->name }}</span>

            @if($this->runningCount > 0)
                <span class="ml-auto text-xs bg-green-900/50 text-green-400 px-2 py-0.5 rounded-full border border-green-800 shrink-0">
                    {{ $this->runningCount }}
                </span>
            @endif
        </button>

        <button
            wire:click="addCommand"
            class="opacity-0 group-hover:opacity-100 p-1.5 hover:bg-gray-600 rounded text-gray-400 hover:text-white transition-all shrink-0 mr-1"
            title="Add Command"
        >
            <span class="text-xs font-bold">+C</span>
        </button>
    </div>

    <!-- Commands List -->
    @if($expanded)
        <div class="ml-4 mt-0.5 space-y-0.5 border-l-2 border-gray-700/50 pl-2">
            @foreach($project->commands as $command)
                <button
                    wire:click="selectCommand({{ $command->id }})"
                    wire:key="cmd-{{ $command->id }}"
                    class="w-full flex items-center gap-2 py-1.5 px-2 rounded text-sm text-left transition-all
                           {{ $selectedCommandId === $command->id
                              ? 'bg-blue-600 text-white shadow-sm' : 'text-gray-300 hover:bg-gray-700/50' }}"
                >
                    <span class="w-2 h-2 rounded-full shrink-0 {{ $command->status === 'running' ? 'bg-green-400' : 'bg-gray-500' }}"></span>
                    <span class="truncate flex-1">{{ $command->name }}</span>
                </button>
            @endforeach

            @if($project->commands->isEmpty())
                <div class="text-xs text-gray-500 py-2 px-2 italic">
                    No commands yet
                </div>
            @endif
        </div>
    @endif
</div>
