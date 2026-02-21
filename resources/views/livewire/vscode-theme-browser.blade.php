<div>
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

    <!-- Loading Overlay -->
    <div wire:loading.delay wire:target="search, updatedSearchQuery" class="flex items-center justify-center py-12">
        <div class="text-center">
            <x-icon name="loader" class="w-6 h-6 text-primary animate-spin mx-auto mb-2" />
            <p class="text-xs text-muted-foreground">Loading themes...</p>
        </div>
    </div>

    <!-- Theme Grid -->
    <div wire:loading.remove wire:target="search, updatedSearchQuery"
        class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-3">
        @foreach($extensions as $ext)
            <div class="bg-card border border-border hover:border-primary/30 transition-colors group"
                wire:key="ext-{{ $ext['publisherName'] }}-{{ $ext['name'] }}">
                <!-- Extension Header -->
                <div class="p-3 border-b border-border">
                    <div class="flex items-start justify-between gap-2">
                        <div class="min-w-0">
                            <h3 class="text-sm font-medium text-foreground truncate">{{ $ext['displayName'] }}</h3>
                            <p class="text-[10px] text-muted-foreground truncate">
                                {{ $ext['publisherDisplayName'] ?? $ext['publisherName'] }}</p>
                        </div>
                        <x-badge>{{ $ext['totalThemes'] ?? count($ext['themes'] ?? []) }} themes</x-badge>
                    </div>
                    @if(!empty($ext['shortDescription']))
                        <p class="text-[10px] text-muted-foreground mt-1.5 line-clamp-2">{{ $ext['shortDescription'] }}</p>
                    @endif
                </div>

                <!-- Theme Variants with SVG Previews -->
                <div class="p-2 space-y-2 max-h-96 overflow-y-auto">
                    @foreach(($ext['themes'] ?? []) as $theme)
                        <div class="border border-border hover:border-primary/30 transition-colors group/theme overflow-hidden"
                            wire:key="theme-{{ $ext['publisherName'] }}-{{ $ext['name'] }}-{{ $theme['name'] }}">
                            <!-- SVG Preview Image -->
                            @if(!empty($theme['url']))
                                <div class="w-full aspect-video overflow-hidden bg-background"
                                    style="background-color: {{ $theme['editorBackground'] ?? '#1e1e2e' }}">
                                    <img src="{{ $theme['url'] }}" alt="{{ $theme['displayName'] ?? $theme['name'] }} preview"
                                        class="w-full h-full object-cover object-top" loading="lazy">
                                </div>
                            @else
                                {{-- Fallback: color swatch if no SVG --}}
                                <div class="w-full h-16" style="background-color: {{ $theme['editorBackground'] ?? '#1e1e2e' }}">
                                </div>
                            @endif

                            <!-- Theme Info + Download -->
                            <div class="flex items-center justify-between gap-2 p-2 bg-card">
                                <div class="flex items-center gap-2 min-w-0">
                                    <div class="w-4 h-4 shrink-0 border border-border"
                                        style="background-color: {{ $theme['editorBackground'] ?? '#1e1e2e' }}"></div>
                                    <div class="min-w-0">
                                        <span
                                            class="text-xs text-foreground truncate block">{{ $theme['displayName'] ?? $theme['name'] }}</span>
                                    </div>
                                </div>

                                @if($downloadingTheme === "{$ext['publisherName']}.{$ext['name']}.{$theme['name']}")
                                    <x-icon name="loader" class="w-3.5 h-3.5 text-primary animate-spin shrink-0" />
                                @else
                                    <button
                                        wire:click="downloadTheme('{{ $ext['publisherName'] }}', '{{ $ext['name'] }}', '{{ $theme['displayName'] ?? $theme['name'] }}', '{{ $theme['url'] ?? '' }}')"
                                        class="opacity-0 group-hover/theme:opacity-100 text-muted-foreground hover:text-primary transition-all shrink-0 p-1"
                                        title="Download & activate">
                                        <x-icon name="download" class="w-3.5 h-3.5" />
                                    </button>
                                @endif
                            </div>
                        </div>
                    @endforeach

                    @if(empty($ext['themes']))
                        <div class="flex items-center justify-between p-1.5">
                            <span class="text-xs text-muted-foreground">No theme variants listed</span>
                            @if($downloadingTheme === "{$ext['publisherName']}.{$ext['name']}")
                                <x-icon name="loader" class="w-3.5 h-3.5 text-primary animate-spin" />
                            @else
                                <button wire:click="downloadTheme('{{ $ext['publisherName'] }}', '{{ $ext['name'] }}')"
                                    class="text-muted-foreground hover:text-primary transition-colors"
                                    title="Download default theme">
                                    <x-icon name="download" class="w-3.5 h-3.5" />
                                </button>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        @endforeach
    </div>

    @if($hasSearched && empty($extensions))
        <div class="text-center py-12 text-muted-foreground">
            <x-icon name="search" class="w-8 h-8 mx-auto mb-2 opacity-30" />
            <p class="text-sm">No themes found for "{{ $searchQuery }}"</p>
        </div>
    @endif
</div>