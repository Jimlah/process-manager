@props(['text', 'position' => 'top'])

@php
    $positionClasses = [
        'top' => 'bottom-full left-1/2 -translate-x-1/2 mb-2',
        'bottom' => 'top-full left-1/2 -translate-x-1/2 mt-2',
        'left' => 'right-full top-1/2 -translate-y-1/2 mr-2',
        'right' => 'left-full top-1/2 -translate-y-1/2 ml-2',
    ][$position] ?? 'bottom-full left-1/2 -translate-x-1/2 mb-2';

    $arrowClasses = [
        'top' => '-bottom-0.5 left-1/2 -translate-x-1/2',
        'bottom' => '-top-0.5 left-1/2 -translate-x-1/2',
        'left' => '-right-0.5 top-1/2 -translate-y-1/2',
        'right' => '-left-0.5 top-1/2 -translate-y-1/2',
    ][$position] ?? '-bottom-0.5 left-1/2 -translate-x-1/2';
@endphp

<div x-data="{ show: false }" @mouseenter="show = true" @mouseleave="show = false" class="relative inline-block">
    {{ $slot }}

    <div x-show="show" x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="absolute z-50 px-2 py-1 text-[10px] font-bold uppercase tracking-wider text-accent-foreground bg-accent border border-border shadow-md pointer-events-none whitespace-nowrap {{ $positionClasses }}"
        style="display: none;">
        {{ $text }}
        <div
            class="absolute w-1.5 h-1.5 bg-accent border-b border-r border-border transform rotate-45 {{ $arrowClasses }}">
        </div>
    </div>
</div>