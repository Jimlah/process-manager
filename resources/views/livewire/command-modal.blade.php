<div>
    @if($show)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 backdrop-blur-sm" wire:click.self="cancel">
            <div class="bg-gray-800 rounded-lg shadow-2xl border border-gray-700 w-full max-w-lg mx-4">
                <div class="p-6">
                    <h2 class="text-xl font-semibold mb-2">
                        {{ $command ? 'Edit Command' : 'New Command' }}
                    </h2>

                    @if($project)
                        <p class="text-sm text-gray-400 mb-4">
                            Project: <span class="text-blue-400">{{ $project->name }}</span>
                        </p>
                    @endif

                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-400 mb-1">Command Name</label>
                            <input
                                type="text"
                                wire:model="name"
                                class="w-full px-3 py-2 bg-gray-900 border border-gray-700 rounded focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                                placeholder="e.g., Vite Dev Server"
                            />
                            @error('name')
                                <span class="text-red-400 text-sm mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-400 mb-1">Command</label>
                            <textarea
                                wire:model="commandText"
                                rows="3"
                                class="w-full px-3 py-2 bg-gray-900 border border-gray-700 rounded focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 font-mono text-sm resize-none"
                                placeholder="npm run dev"
                            ></textarea>
                            @error('commandText')
                                <span class="text-red-400 text-sm mt-1">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-3 mt-6">
                        @if($command)
                            <button
                                wire:click="delete"
                                class="px-4 py-2 text-red-400 hover:text-red-300 hover:bg-red-900/20 rounded text-sm font-medium transition-colors"
                            >
                                Delete
                            </button>
                        @endif

                        <div class="flex-1"></div>

                        <button
                            wire:click="cancel"
                            class="px-4 py-2 text-gray-400 hover:text-white hover:bg-gray-700 rounded text-sm font-medium transition-colors"
                        >
                            Cancel
                        </button>

                        <button
                            wire:click="save"
                            class="px-4 py-2 bg-blue-600 hover:bg-blue-700 active:bg-blue-800 rounded text-sm font-medium transition-colors"
                        >
                            {{ $command ? 'Update' : 'Create' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
