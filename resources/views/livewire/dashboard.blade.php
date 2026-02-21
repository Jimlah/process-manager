<div class="flex flex-col h-full" x-data="{}">
    <!-- Top Navbar: Running Processes -->
    <nav class="bg-gray-800 border-b border-gray-700 px-4 py-2 flex items-center gap-4 shrink-0">
        <h1 class="font-bold text-lg shrink-0">Dev Process Manager</h1>
        <div class="flex-1 flex gap-2 overflow-x-auto no-scrollbar">
            @forelse($this->runningCommands as $cmd)
                <button
                    wire:click="selectCommand({{ $cmd->id }})"
                    wire:key="nav-cmd-{{ $cmd->id }}"
                    class="px-3 py-1.5 bg-green-900/50 hover:bg-green-900 border border-green-700 text-green-300 rounded-full text-sm transition-colors whitespace-nowrap flex items-center gap-2"
                >
                    <span class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></span>
                    <span class="font-medium">{{ $cmd->project->name }}</span>
                    <span class="text-green-400/60">/</span>
                    <span>{{ $cmd->name }}</span>
                </button>
            @empty
                <span class="text-gray-500 text-sm py-1.5">No running processes</span>
            @endforelse
        </div>
    </nav>

    <div class="flex flex-1 overflow-hidden">
        <!-- Sidebar: Tree Navigation -->
        <aside class="w-72 bg-gray-800 border-r border-gray-700 flex flex-col shrink-0">
            <div class="p-4 border-b border-gray-700">
                <h2 class="font-semibold text-gray-400 uppercase text-xs tracking-wider">Projects</h2>
            </div>

            <div class="flex-1 overflow-y-auto p-2 space-y-1">
                @foreach($projects as $project)
                    <livewire:project-tree
                        :project="$project"
                        :selected-command-id="$selectedCommandId"
                        :key="'project-'.$project->id"
                    />
                @endforeach
            </div>

            <div class="p-4 border-t border-gray-700">
                <button
                    wire:click="openProjectModal"
                    class="w-full py-2 px-4 bg-blue-600 hover:bg-blue-700 active:bg-blue-800 rounded text-sm font-medium transition-colors flex items-center justify-center gap-2"
                >
                    <span>+</span>
                    <span>New Project</span>
                </button>
            </div>
        </aside>

        <!-- Main Content: Log Viewer -->
        <main class="flex-1 bg-gray-900 p-6 overflow-hidden flex flex-col">
            @if($this->selectedCommand)
                <livewire:process-runner
                    :command="$this->selectedCommand"
                    :key="'runner-'.$selectedCommandId"
                />
            @else
                <div class="flex-1 flex items-center justify-center text-gray-500">
                    <div class="text-center">
                        <svg class="w-16 h-16 mx-auto mb-4 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 9l3 3-3 3m5 0h3M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <p class="text-lg font-medium">Select a command to view logs</p>
                        <p class="text-sm mt-2 text-gray-600">Choose a project from the sidebar and click on a command</p>
                    </div>
                </div>
            @endif
        </main>
    </div>

    <!-- Project Modal -->
    @if($showProjectModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 backdrop-blur-sm" wire:click.self="closeProjectModal">
            <div class="bg-gray-800 rounded-lg shadow-2xl border border-gray-700 w-full max-w-md mx-4">
                <div class="p-6">
                    <h2 class="text-xl font-semibold mb-4">New Project</h2>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-400 mb-1">Project Name</label>
                            <input
                                type="text"
                                wire:model="projectName"
                                class="w-full px-3 py-2 bg-gray-900 border border-gray-700 rounded focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                                placeholder="e.g., eClinic"
                            />
                            @error('projectName')
                                <span class="text-red-400 text-sm mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-400 mb-1">Project Path</label>
                            <div class="flex gap-2">
                                <input
                                    type="text"
                                    wire:model="projectPath"
                                    class="flex-1 px-3 py-2 bg-gray-900 border border-gray-700 rounded focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 font-mono text-sm"
                                    placeholder="/Users/{{ get_current_user() }}/Projects/eclinic"
                                />
                                <button
                                    type="button"
                                    wire:click="selectProjectPath"
                                    class="px-3 py-2 bg-gray-700 hover:bg-gray-600 border border-gray-600 rounded text-sm font-medium transition-colors"
                                >
                                    Browse...
                                </button>
                            </div>
                            @error('projectPath')
                                <span class="text-red-400 text-sm mt-1">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-3 mt-6">
                        <button
                            wire:click="closeProjectModal"
                            class="px-4 py-2 text-gray-400 hover:text-white hover:bg-gray-700 rounded text-sm font-medium transition-colors"
                        >
                            Cancel
                        </button>

                        <button
                            wire:click="saveProject"
                            class="px-4 py-2 bg-blue-600 hover:bg-blue-700 active:bg-blue-800 rounded text-sm font-medium transition-colors"
                        >
                            Create
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Command Modal -->
    @if($showCommandModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 backdrop-blur-sm" wire:click.self="closeCommandModal">
            <div class="bg-gray-800 rounded-lg shadow-2xl border border-gray-700 w-full max-w-lg mx-4">
                <div class="p-6">
                    <h2 class="text-xl font-semibold mb-2">New Command</h2>

                    @if($commandModalProjectId)
                        <p class="text-sm text-gray-400 mb-4">
                            Project: <span class="text-blue-400">{{ App\Models\Project::find($commandModalProjectId)?->name }}</span>
                        </p>
                    @endif

                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-400 mb-1">Command Name</label>
                            <input
                                type="text"
                                wire:model="commandName"
                                class="w-full px-3 py-2 bg-gray-900 border border-gray-700 rounded focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                                placeholder="e.g., Vite Dev Server"
                            />
                            @error('commandName')
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
                        <button
                            wire:click="closeCommandModal"
                            class="px-4 py-2 text-gray-400 hover:text-white hover:bg-gray-700 rounded text-sm font-medium transition-colors"
                        >
                            Cancel
                        </button>

                        <button
                            wire:click="saveCommand"
                            class="px-4 py-2 bg-blue-600 hover:bg-blue-700 active:bg-blue-800 rounded text-sm font-medium transition-colors"
                        >
                            Create
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
