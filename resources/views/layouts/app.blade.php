<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SIGNAL</title>

    {{-- Inline theme init to prevent FOUC --}}
    <script>
        (function () {
            var t = localStorage.getItem('theme') || 'dark';
            var isDark = t === 'dark' || (t === 'system' && window.matchMedia('(prefers-color-scheme: dark)').matches);
            if (isDark) document.documentElement.classList.add('dark');
        })();
    </script>

    {{-- Custom theme CSS file --}}
    <link rel="stylesheet" href="{{ \App\Services\ThemeService::getThemeUrl() }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body
    class="bg-background text-foreground h-screen w-screen overflow-hidden flex font-sans antialiased selection:bg-primary/30">
    {{ $slot }}
</body>

</html>