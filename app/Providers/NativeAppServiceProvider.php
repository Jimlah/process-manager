<?php

namespace App\Providers;

use Native\Desktop\Contracts\ProvidesPhpIni;
use Native\Desktop\Facades\Window;

class NativeAppServiceProvider implements ProvidesPhpIni
{
    /**
     * Executed once the native application has been booted.
     * Use this method to open windows, register global shortcuts, etc.
     */
    public function boot(): void
    {
        Window::open()
            ->width(1400)
            ->height(900)
            ->minWidth(1000)
            ->minHeight(600)
            ->title('SIGNAL');

        \App\Models\Command::where('auto_start', true)->each(function ($command) {
            \App\Events\CommandStartRequested::dispatch($command);
        });
    }

    /**
     * Return an array of php.ini directives to be set.
     */
    public function phpIni(): array
    {
        return [
        ];
    }
}
