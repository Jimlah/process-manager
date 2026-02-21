<div wire:init="loadPopularThemes">
    <!-- Search Bar -->
    <div class="mb-4">
        <div class="relative">
            <x-icon name="search" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-muted-foreground" />
            <input wire:model.live.debounce.500ms="searchQuery" type="text" placeholder="Search VS Code themes..."
                class="w-full h-9 bg-background pl-10 pr-3 py-1 text-sm shadow-sm transition-colors placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring border border-input">
        </div>
        @if($hasSearched)
            <div class="text-[10px] text-muted-foreground mt-1.5">
                {{ number_format($totalResults) }} themes available
                <span wire:loading wire:target="search, updatedSearchQuery" class="text-primary ml-1">Searching...</span>
            </div>
        @endif
    </div>

    <!-- Status -->
    @if($downloadStatus)
        @php
            $statusClass = match (true) {
                str_contains($downloadStatus, 'downloaded'), str_contains($downloadStatus, 'activated') => 'text-success bg-success/5 border-success/20',
                str_contains($downloadStatus, 'Failed'), str_contains($downloadStatus, 'failed') => 'text-destructive bg-destructive/5 border-destructive/20',
                default => 'text-muted-foreground bg-muted/5 border-border',
            };
        @endphp
        <div class="text-xs p-2.5 mb-4 border {{ $statusClass }}">
            {{ $downloadStatus }}
        </div>
    @endif

    <!-- Not yet loaded state -->
    @if(!$hasSearched)
        <div class="flex items-center justify-center py-12">
            <div class="text-center">
                <x-icon name="loader" class="w-6 h-6 text-primary animate-spin mx-auto mb-2" />
                <p class="text-xs text-muted-foreground">Loading themes...</p>
            </div>
        </div>
    @else
        <!-- Loading during search -->
        <div wire:loading.delay wire:target="search, updatedSearchQuery" class="flex items-center justify-center py-8">
            <div class="text-center">
                <x-icon name="loader" class="w-5 h-5 text-primary animate-spin mx-auto mb-2" />
                <p class="text-xs text-muted-foreground">Searching...</p>
            </div>
        </div>

        <!-- Theme List -->
        <div wire:loading.remove wire:target="search, updatedSearchQuery" class="space-y-3">
            @foreach($extensions as $ext)
                <div class="bg-card border border-border" wire:key="ext-{{ $ext['publisherName'] }}-{{ $ext['name'] }}">
                    <!-- Extension Header -->
                    <div class="px-3 py-2.5 border-b border-border flex items-center justify-between gap-3">
                        <div class="min-w-0">
                            <div class="flex items-center gap-2">
                                <h3 class="text-sm font-medium text-foreground truncate">{{ $ext['displayName'] }}</h3>
                                <x-badge>{{ $ext['totalThemes'] ?? count($ext['themes'] ?? []) }}</x-badge>
                            </div>
                            <p class="text-[10px] text-muted-foreground truncate">
                                {{ $ext['publisherDisplayName'] ?? $ext['publisherName'] }}</p>
                        </div>
                    </div>

                    <!-- Theme Variants -->
                    <div class="divide-y divide-border">
                        @foreach(($ext['themes'] ?? []) as $theme)
                            <div class="flex items-center gap-3 px-3 py-2 hover:bg-muted/20 transition-colors"
                                wire:key="theme-{{ $ext['publisherName'] }}-{{ $ext['name'] }}-{{ $theme['name'] }}">
                                <!-- SVG Preview -->
                                @if(!empty($theme['url']))
                                    <div class="w-32 h-20 shrink-0 border border-border overflow-hidden"
                                        style="background-color: {{ $theme['editorBackground'] ?? '#1e1e2e' }}">
                                        <img src="{{ $theme['url'] }}" alt="{{ $theme['displayName'] ?? $theme['name'] }}"
                                            class="w-full h-full object-cover object-top" loading="lazy">
                                    </div>
                                @else
                                    <div class="w-32 h-20 shrink-0 border border-border"
                                        style="background-color: {{ $theme['editorBackground'] ?? '#1e1e2e' }}"></div>
                                @endif

                                <!-- Info -->
                                <div class="flex-1 min-w-0">
                                    <span
                                        class="text-xs font-medium text-foreground block truncate">{{ $theme['displayName'] ?? $theme['name'] }}</span>
                                    <span
                                        class="text-[10px] text-muted-foreground font-mono">{{ $theme['editorBackground'] ?? '' }}</span>
                                </div>

                                <!-- Download Button (always visible) -->
                                @if($downloadingTheme === "{$ext['publisherName']}.{$ext['name']}.{$theme['name']}")
                                    <x-icon name="loader" class="w-4 h-4 text-primary animate-spin shrink-0" />
                                @else
                                    <button
                                        wire:click="downloadTheme('{{ $ext['publisherName'] }}', '{{ $ext['name'] }}', '{{ $theme['displayName'] ?? $theme['name'] }}', '{{ $theme['url'] ?? '' }}')"
                                        class="shrink-0 inline-flex items-center gap-1.5 px-2.5 py-1.5 text-[10px] font-medium bg-primary/10 text-primary border border-primary/20 hover:bg-primary/20 transition-colors"
                                        title="Download & activate">
                                        <x-icon name="download" class="w-3 h-3" />
                                        Download
                                    </button>
                                @endif
                            </div>
                        @endforeach

                        @if(empty($ext['themes']))
                            <div class="flex items-center justify-between px-3 py-2">
                                <span class="text-xs text-muted-foreground">Default theme</span>
                                @if($downloadingTheme === "{$ext['publisherName']}.{$ext['name']}")
                                    <x-icon name="loader" class="w-4 h-4 text-primary animate-spin" />
                                @else
                                    <button wire:click="downloadTheme('{{ $ext['publisherName'] }}', '{{ $ext['name'] }}')"
                                        class="inline-flex items-center gap-1.5 px-2.5 py-1.5 text-[10px] font-medium bg-primary/10 text-primary border border-primary/20 hover:bg-primary/20 transition-colors"
                                        title="Download default theme">
                                        <x-icon name="download" class="w-3 h-3" />
                                        Download
                                    </button>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        @if(empty($extensions))
            <div class="text-center py-12 text-muted-foreground">
                <x-icon name="search" class="w-8 h-8 mx-auto mb-2 opacity-30" />
                <p class="text-sm">No themes found for "{{ $searchQuery }}"</p>
            </div>
        @endif
    @endif
</div>