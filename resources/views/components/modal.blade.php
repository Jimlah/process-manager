@props(['show' => false, 'title' => '', 'maxWidth' => 'max-w-md'])

@if($show)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-background/80 backdrop-blur-sm" {{ $attributes->whereStartsWith('wire:click') }}>
        <div class="bg-popover border border-border w-full {{ $maxWidth }} shadow-2xl shadow-black/50 flex flex-col mx-4" @click.stop>
            <div class="flex items-center justify-between px-4 py-3 border-b border-border bg-card">
                <h2 class="text-sm font-bold text-foreground flex items-center gap-2">
                    <x-icon name="terminal" class="w-4 h-4" />
                    {{ $title }}
                </h2>
                @if(isset($close))
                    {{ $close }}
                @endif
            </div>
            <div class="p-6">
                {{ $slot }}
            </div>
        </div>
    </div>
@endif