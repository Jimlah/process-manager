<div class="flex h-full w-full overflow-hidden">
    <!-- Sidebar: Theme List -->
    <aside class="w-80 bg-card border-r border-border flex flex-col shrink-0 overflow-hidden">
        <div class="p-4 border-b border-border flex items-center justify-between">
            <h2 class="font-bold text-foreground tracking-widest text-sm flex items-center gap-2">
                <x-icon name="settings" class="w-4 h-4 text-primary" />
                SETTINGS
            </h2>
            <a href="/dashboard" class="text-muted-foreground hover:text-primary transition-colors">
                <x-icon name="arrow-left" class="w-4 h-4" />
            </a>
        </div>

        <div class="flex-1 overflow-y-auto p-2 space-y-2">
            <div class="text-xs font-bold text-muted-foreground uppercase tracking-wider px-2 py-2">Themes</div>
            
            @foreach($themes as $theme)
                @php
                    $bgColor = $theme->colors['editor.background'] ?? ($theme->is_builtin ? ($theme->slug === 'light' ? '#eff1f5' : '#1e1e2e') : '#2b2b2a');
                    $isSelected = $selectedThemeId === $theme->id;
                @endphp
                <div 
                    wire:click="activateTheme({{ $theme->id }})"
                    @class([
                        'p-3 cursor-pointer transition-colors border-l-2',
                        'bg-primary/10 border-primary' => $isSelected,
                        'hover:bg-muted/50 border-transparent' => !$isSelected,
                    ])
                >
                    <div class="flex items-center justify-between mb-2">
                        <div class="flex items-center gap-2">
                            <div 
                                class="w-4 h-4 rounded-sm border border-border"
                                style="background-color: {{ $bgColor }}"
                            ></div>
                            <span @class(['text-sm font-medium', 'text-primary' => $isSelected, 'text-foreground' => !$isSelected])>
                                {{ $theme->name }}
                            </span>
                        </div>
                        @if($theme->is_builtin)
                            <span class="text-[10px] text-muted-foreground bg-muted px-1.5 py-0.5 rounded">Built-in</span>
                        @else
                            <button 
                                wire:click.stop="deleteTheme({{ $theme->id }})"
                                class="text-muted-foreground hover:text-destructive transition-colors"
                                title="Delete theme"
                            >
                                <x-icon name="trash" class="w-3.5 h-3.5" />
                            </button>
                        @endif
                    </div>
                    
                    @if($theme->preview_url)
                        <div class="mt-2 rounded border border-border overflow-hidden bg-background">
                            <img src="{{ $theme->preview_url }}" alt="{{ $theme->name }} preview" class="w-full h-20 object-cover object-top">
                        </div>
                    @endif
                </div>
            @endforeach

            @if($themes->isEmpty())
                <div class="text-xs text-muted-foreground p-4 text-center">No themes available.</div>
            @endif
        </div>
    </aside>

    <!-- Main Content: Theme Import & Preview -->
    <main class="flex-1 bg-background overflow-hidden flex flex-col">
        <div class="p-6 overflow-y-auto">
            <h1 class="text-xl font-bold text-foreground mb-6">Theme Settings</h1>

            <!-- Import Section -->
            <div class="bg-card border border-border p-6 mb-6">
                <h2 class="text-sm font-bold text-foreground uppercase tracking-wider mb-4 flex items-center gap-2">
                    <x-icon name="upload" class="w-4 h-4" />
                    Import VS Code Theme
                </h2>
                
                <p class="text-sm text-muted-foreground mb-4">
                    Import a VS Code theme JSON file. You can download themes from 
                    <a href="https://vscodethemes.com" target="_blank" class="text-primary hover:underline">vscodethemes.com</a> 
                    or extract them from VS Code extensions.
                </p>

                <div class="space-y-4">
                    <div class="flex gap-2">
                        <x-button variant="outline" wire:click="loadThemeFromFile" class="flex items-center gap-2">
                            <x-icon name="file" class="w-4 h-4" />
                            Load from File
                        </x-button>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-muted-foreground mb-1 uppercase tracking-wider">Or paste theme JSON</label>
                        <textarea
                            wire:model="themeJson"
                            rows="8"
                            class="flex w-full bg-background px-3 py-2 text-xs font-mono shadow-sm transition-colors placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring border border-input resize-none"
                            placeholder='{"name": "My Theme", "colors": {"editor.background": "#1e1e2e", ...}}'
                        ></textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-muted-foreground mb-1 uppercase tracking-wider">Preview Image URL (optional)</label>
                        <x-input wire:model="previewUrl" placeholder="https://images.vscodethemes.com/..." />
                        <p class="text-[10px] text-muted-foreground mt-1">Paste the preview URL from vscodethemes.com</p>
                    </div>

                    @if($importStatus)
                        @php
                            $statusClass = match(true) {
                                str_contains($importStatus, 'success'), str_contains($importStatus, 'activated') => 'text-success',
                                str_contains($importStatus, 'Failed'), str_contains($importStatus, 'Invalid') => 'text-destructive',
                                default => 'text-muted-foreground',
                            };
                        @endphp
                        <div class="text-sm {{ $statusClass }}">
                            {{ $importStatus }}
                        </div>
                    @endif

                    <div class="flex justify-end">
                        <x-button wire:click="importTheme" :disabled="empty($themeJson)">Import Theme</x-button>
                    </div>
                </div>
            </div>

            <!-- Preview Section -->
            @if($selectedThemeId)
                @php
                    $activeTheme = $themes->firstWhere('id', $selectedThemeId);
                @endphp
                @if($activeTheme)
                    @php
                        $colorMap = [
                            'Background' => $activeTheme->colors['editor.background'] ?? ($activeTheme->is_builtin ? ($activeTheme->slug === 'light' ? '#eff1f5' : '#1e1e2e') : null),
                            'Foreground' => $activeTheme->colors['editor.foreground'] ?? ($activeTheme->is_builtin ? ($activeTheme->slug === 'light' ? '#4c4f69' : '#cdd6f4') : null),
                            'Sidebar' => $activeTheme->colors['sideBar.background'] ?? ($activeTheme->colors['activityBar.background'] ?? ($activeTheme->is_builtin ? ($activeTheme->slug === 'light' ? '#e6e9ef' : '#181825') : null)),
                            'Primary' => $activeTheme->colors['button.background'] ?? ($activeTheme->colors['activityBarBadge.background'] ?? ($activeTheme->is_builtin ? ($activeTheme->slug === 'light' ? '#8839ef' : '#cba6f7') : null)),
                            'Secondary' => $activeTheme->colors['input.background'] ?? ($activeTheme->is_builtin ? ($activeTheme->slug === 'light' ? '#ccd0da' : '#313244') : null),
                            'Border' => $activeTheme->colors['panel.border'] ?? ($activeTheme->colors['sideBar.border'] ?? ($activeTheme->is_builtin ? ($activeTheme->slug === 'light' ? '#bcc0cc' : '#313244') : null)),
                            'Accent' => $activeTheme->colors['list.activeSelectionBackground'] ?? ($activeTheme->is_builtin ? ($activeTheme->slug === 'light' ? '#ccd0da' : '#313244') : null),
                            'Terminal' => $activeTheme->colors['terminal.background'] ?? ($activeTheme->colors['editor.background'] ?? ($activeTheme->is_builtin ? ($activeTheme->slug === 'light' ? '#dce0e8' : '#000000') : null)),
                        ];
                    @endphp
                    <div class="bg-card border border-border p-6">
                        <h2 class="text-sm font-bold text-foreground uppercase tracking-wider mb-4 flex items-center gap-2">
                            <x-icon name="eye" class="w-4 h-4" />
                            Theme Preview: {{ $activeTheme->name }}
                        </h2>

                        @if($activeTheme->preview_url)
                            <div class="mb-6 rounded border border-border overflow-hidden bg-background">
                                <img src="{{ $activeTheme->preview_url }}" alt="{{ $activeTheme->name }} preview" class="w-full h-48 object-cover object-top">
                            </div>
                        @endif

                        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                            @foreach($colorMap as $label => $color)
                                @if($color)
                                    <div class="flex items-center gap-2 p-2 bg-background border border-border">
                                        <div 
                                            class="w-8 h-8 rounded-sm border border-border shrink-0"
                                            style="background-color: {{ $color }}"
                                        ></div>
                                        <div class="min-w-0">
                                            <div class="text-xs font-medium text-foreground truncate">{{ $label }}</div>
                                            <div class="text-[10px] text-muted-foreground font-mono truncate">{{ $color }}</div>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                @endif
            @endif
        </div>
    </main>
</div>
