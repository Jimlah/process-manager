<?php

namespace App\Http\Middleware;

use App\Services\ThemeService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class InjectThemeCss
{
    public function handle(Request $request, Closure $next): Response
    {
        // Ensure theme file exists
        if (! ThemeService::themeFileExists()) {
            ThemeService::writeThemeFile();
        }

        return $next($request);
    }
}
