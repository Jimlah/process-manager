@props(['variant' => 'default'])

@php
$baseClasses = 'inline-flex items-center border px-1.5 py-0.5 text-xs font-semibold transition-colors focus:outline-none focus:ring-1 focus:ring-ring';

$variants = [
    'default' => 'border-primary bg-primary/10 text-primary',
    'secondary' => 'border-secondary bg-secondary/10 text-secondary-foreground',
    'destructive' => 'border-destructive/20 bg-destructive/10 text-destructive',
    'outline' => 'text-foreground',
    'success' => 'border-success/20 bg-success/10 text-success',
];

$classes = $baseClasses . ' ' . ($variants[$variant] ?? $variants['default']);
@endphp

<div {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</div>