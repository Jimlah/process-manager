<div class="flex h-full w-full overflow-hidden">
    <!-- Sidebar: Settings Navigation -->
    <aside class="w-64 bg-card border-r border-border flex flex-col shrink-0 overflow-hidden">
        <div class="p-4 border-b border-border flex items-center justify-between">
            <h2 class="font-bold text-foreground tracking-widest text-sm flex items-center gap-2">
                <x-icon name="settings" class="w-4 h-4 text-primary" />
                SETTINGS
            </h2>
            <a href="/" class="text-muted-foreground hover:text-primary transition-colors">
                <x-icon name="arrow-left" class="w-4 h-4" />
            </a>
        </div>

        <div class="flex-1 overflow-y-auto p-2 space-y-1">
            {{-- Section divider --}}
            <div class="px-3 py-1.5 text-[9px] text-muted-foreground tracking-widest">-- MENU --</div>

            <!-- Themes Tab -->
            <button wire:click="setTab('themes')" @class([
                'w-full flex items-center gap-2.5 px-3 py-2 text-xs font-medium transition-colors text-left',
                'bg-primary text-primary-foreground' => $activeTab === 'themes',
                'text-muted-foreground hover:text-foreground hover:bg-muted/30' => $activeTab !== 'themes',
            ])>
                <x-icon name="palette" class="w-4 h-4" />
                Appearance
            </button>

            <!-- General Tab -->
            <button wire:click="setTab('general')" @class([
                'w-full flex items-center gap-2.5 px-3 py-2 text-xs font-medium transition-colors text-left',
                'bg-primary text-primary-foreground' => $activeTab === 'general',
                'text-muted-foreground hover:text-foreground hover:bg-muted/30' => $activeTab !== 'general',
            ])>
                <x-icon name="settings" class="w-4 h-4" />
                General
            </button>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 bg-background overflow-hidden flex flex-col">
        <div class="flex-1 overflow-y-auto p-6">

            @if($activeTab === 'themes')
                {{-- Breadcrumb --}}
                <div class="text-[10px] text-muted-foreground mb-4 tracking-wide">~/settings/appearance</div>

                {{-- ===== THEMES TAB ===== --}}
                <h1 class="text-xl font-bold text-primary uppercase tracking-wider mb-1">Appearance & Themes</h1>
                <p class="text-[10px] text-muted-foreground mb-6">Theme management & customization</p>

                {{-- Installed Themes --}}
                <div class="bg-card border border-border p-5 mb-6">
                    <h2 class="text-[10px] text-muted-foreground tracking-widest mb-4">-- INSTALLED_THEMES --</h2>

                    @if($themes->isEmpty())
                        <div class="text-xs text-muted-foreground text-center py-6">No themes installed.</div>
                    @else
                        <div class="space-y-0.5">
                            @foreach($themes as $theme)
                                @php
                                    $bgColor = $theme->colors['editor.background'] ?? ($theme->is_builtin ? ($theme->slug === 'light' ? '#eff1f5' : '#1e1e2e') : '#2b2b2a');
                                    $fgColor = $theme->colors['editor.foreground'] ?? '#cdd6f4';
                                    $isSelected = $selectedThemeId === $theme->id;
                                @endphp
                                <button wire:click="activateTheme({{ $theme->id }})" wire:key="installed-theme-{{ $theme->id }}"
                                    @class([
                                        'w-full flex items-center gap-3 px-3 py-2.5 text-left transition-colors group',
                                        'bg-primary/10 border border-primary/40' => $isSelected,
                                        'hover:bg-muted/20 border border-transparent' => !$isSelected,
                                    ])>
                                    {{-- Color preview --}}
                                    <div class="w-10 h-7 shrink-0 ring-1 ring-white/10 overflow-hidden flex"
                                        style="background-color: {{ $bgColor }}">
                                        <span class="m-auto text-[7px] font-mono leading-none"
                                            style="color: {{ $fgColor }}">Aa</span>
                                    </div>

                                    {{-- Name --}}
                                    <span @class([
                                        'text-xs font-medium truncate flex-1 uppercase tracking-wide',
                                        'text-primary' => $isSelected,
                                        'text-foreground' => !$isSelected,
                                    ])>{{ $theme->name }}</span>

                                    {{-- Status tags --}}
                                    @if($isSelected)
                                        <span class="text-[9px] text-primary font-bold tracking-wider shrink-0">[ ACTIVE ]</span>
                                    @endif
                                    @if($theme->is_builtin)
                                        <span class="text-[9px] text-muted-foreground tracking-wider shrink-0">[ BUILT_IN ]</span>
                                    @endif

                                    {{-- Delete --}}
                                    @if(!$theme->is_builtin)
                                        <span wire:click.stop="deleteTheme({{ $theme->id }})"
                                            class="opacity-0 group-hover:opacity-100 text-muted-foreground hover:text-destructive transition-all cursor-pointer shrink-0"
                                            title="Delete">
                                            <x-icon name="trash" class="w-3 h-3" />
                                        </span>
                                    @endif
                                </button>
                            @endforeach
                        </div>
                    @endif
                </div>

                @if($importStatus)
                    @php
                        $statusClass = match (true) {
                            str_contains($importStatus, 'success'), str_contains($importStatus, 'activated') => 'text-success bg-success/5 border-success/20',
                            str_contains($importStatus, 'Failed'), str_contains($importStatus, 'Invalid') => 'text-destructive bg-destructive/5 border-destructive/20',
                            default => 'text-muted-foreground bg-muted/5 border-border',
                        };
                    @endphp
                    <div class="text-xs p-2.5 mb-4 border {{ $statusClass }} flex items-center gap-2">
                        <span class="text-primary font-bold">>>></span>
                        {{ $importStatus }}
                    </div>
                @endif

                {{-- Browse VS Code Themes --}}
                <div class="bg-card border border-border p-5 mb-6">
                    <h2 class="text-[10px] text-muted-foreground tracking-widest mb-1">-- THEME_BROWSER --</h2>
                    <p class="text-[10px] text-muted-foreground mb-4">
                        Search and download themes from the VS Code Marketplace.
                    </p>
                    <livewire:vscode-theme-browser />
                </div>

                {{-- Import From File / JSON --}}
                <div class="bg-card border border-border p-5">
                    <h2 class="text-[10px] text-muted-foreground tracking-widest mb-1">-- MANUAL_IMPORT --</h2>
                    <p class="text-[10px] text-muted-foreground mb-4">
                        Load a VS Code theme JSON file or paste the JSON directly.
                    </p>

                    <div class="space-y-3">
                        <div class="flex gap-2">
                            <x-button variant="outline" size="sm" wire:click="loadThemeFromFile"
                                class="flex items-center gap-2">
                                <x-icon name="file" class="w-3.5 h-3.5" />
                                Load from File
                            </x-button>
                        </div>

                        <div>
                            <label
                                class="block text-[10px] font-bold text-muted-foreground mb-1 uppercase tracking-wider">Or
                                paste theme JSON</label>
                            <textarea wire:model="themeJson" rows="6"
                                class="flex w-full bg-background px-3 py-2 text-xs font-mono shadow-sm transition-colors placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring border border-input resize-none"
                                placeholder='{"name": "My Theme", "colors": {"editor.background": "#1e1e2e", ...}}'></textarea>
                        </div>

                        <div>
                            <label
                                class="block text-[10px] font-bold text-muted-foreground mb-1 uppercase tracking-wider">Preview
                                Image URL (optional)</label>
                            <x-input wire:model="previewUrl" placeholder="https://images.vscodethemes.com/..." />
                        </div>

                        <div class="flex justify-end">
                            <x-button size="sm" wire:click="importTheme" :disabled="empty($themeJson)">Import
                                Theme</x-button>
                        </div>
                    </div>
                </div>

            @elseif($activeTab === 'general')
                {{-- Breadcrumb --}}
                <div class="text-[10px] text-muted-foreground mb-4 tracking-wide">~/settings/general</div>

                {{-- ===== GENERAL TAB ===== --}}
                <h1 class="text-xl font-bold text-primary uppercase tracking-wider mb-1">General Settings</h1>
                <p class="text-[10px] text-muted-foreground mb-6">Application configuration</p>

                <div class="bg-card border border-border p-5">
                    <div class="flex items-center justify-center py-12 text-muted-foreground">
                        <div class="text-center">
                            <x-icon name="settings" class="w-10 h-10 mx-auto mb-3 opacity-20" />
                            <p class="text-sm font-medium text-foreground/50">Coming Soon</p>
                            <p class="text-xs mt-1">General settings will be available here.</p>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </main>
</div>