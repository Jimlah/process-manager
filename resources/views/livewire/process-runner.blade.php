<div class="flex flex-col h-full">
    <!-- Header -->
    <div class="flex items-center justify-between mb-4 shrink-0">
        <div class="min-w-0">
            <h2 class="text-xl font-semibold truncate">{{ $command->name }}</h2>
            <div class="flex items-center gap-2 text-sm text-gray-400 mt-1">
                <span class="text-blue-400">{{ $command->project->name }}</span>
                <span class="text-gray-600">/</span>
                <code class="font-mono text-xs text-gray-500 bg-gray-800 px-1.5 py-0.5 rounded truncate max-w-md">{{ $command->command }}</code>
            </div>
        </div>

        <div class="flex items-center gap-3 shrink-0">
            <span class="px-3 py-1.5 rounded-full text-sm font-medium border
                {{ $command->status === 'running' ? 'bg-green-900/30 border-green-700 text-green-400' : 'bg-gray-800 border-gray-700 text-gray-400' }}">
                {{ ucfirst($command->status) }}
            </span>

            @if($command->status === 'stopped')
                <button
                    wire:click="start"
                    wire:loading.attr="disabled"
                    class="px-4 py-2 bg-green-600 hover:bg-green-700 active:bg-green-800 disabled:opacity-50 disabled:cursor-not-allowed rounded text-sm font-medium transition-colors flex items-center gap-2"
                >
                    <span>▶</span>
                    <span>Start</span>
                </button>
            @else
                <button
                    wire:click="stop"
                    wire:loading.attr="disabled"
                    class="px-4 py-2 bg-red-600 hover:bg-red-700 active:bg-red-800 disabled:opacity-50 disabled:cursor-not-allowed rounded text-sm font-medium transition-colors flex items-center gap-2"
                >
                    <span>⏹</span>
                    <span>Stop</span>
                </button>
            @endif

            <button
                wire:click="clearLogs"
                class="px-4 py-2 bg-gray-700 hover:bg-gray-600 active:bg-gray-500 rounded text-sm font-medium transition-colors"
            >
                Clear
            </button>
        </div>
    </div>

    <!-- Terminal -->
    <div class="flex-1 bg-black rounded-lg border border-gray-700 overflow-hidden flex flex-col shadow-2xl">
        <div class="flex-1 overflow-y-auto p-4 font-mono text-sm text-gray-300 whitespace-pre-wrap leading-relaxed"
             id="terminal-{{ $command->id }}"
             wire:stream="logs-{{ $command->id }}">
            {{ $logs }}
        </div>
    </div>
</div>
