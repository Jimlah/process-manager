<?php

namespace App\Livewire;

use App\Services\ThemeService;
use App\Services\VSCodeThemeService;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Native\Desktop\Facades\Notification;

class VSCodeThemeBrowser extends Component
{
    public string $searchQuery = '';

    public array $extensions = [];

    public int $totalResults = 0;

    public string $downloadStatus = '';

    public string $downloadingTheme = '';

    public bool $hasSearched = false;

    public function loadPopularThemes(): void
    {
        try {
            $service = app(VSCodeThemeService::class);
            $results = $service->searchVSCodeThemes('', 1, 24);
            $this->extensions = $results['extensions'];
            $this->totalResults = $results['total'];
            $this->hasSearched = true;
        } catch (\Throwable $e) {
            $this->downloadStatus = 'Failed to load themes: '.$e->getMessage();
        }
    }

    public function updatedSearchQuery(): void
    {
        $this->search();
    }

    public function search(): void
    {
        try {
            $service = app(VSCodeThemeService::class);
            $results = $service->searchVSCodeThemes($this->searchQuery, 1, 24);
            $this->extensions = $results['extensions'];
            $this->totalResults = $results['total'];
            $this->hasSearched = true;
            $this->downloadStatus = '';
        } catch (\Throwable $e) {
            $this->downloadStatus = 'Search failed: '.$e->getMessage();
        }
    }

    public function downloadTheme(string $publisher, string $extension, ?string $themeName = null, ?string $previewUrl = null): void
    {
        $this->downloadingTheme = "{$publisher}.{$extension}".($themeName ? ".{$themeName}" : '');

        try {
            $service = app(VSCodeThemeService::class);
            $theme = $service->downloadAndImportTheme($publisher, $extension, $themeName, $previewUrl);

            $theme->activate();
            ThemeService::writeThemeFile();

            $this->downloadStatus = "Theme '{$theme->name}' downloaded and activated!";
            $this->dispatch('theme-downloaded');

            Notification::title('Theme Downloaded')
                ->message("The theme '{$theme->name}' was successfully downloaded and activated.")
                ->show();
        } catch (\Throwable $e) {
            $this->downloadStatus = "Download failed: {$e->getMessage()}";

            Notification::title('Theme Download Failed')
                ->message("Failed to download '{$themeName}'. Check settings page for error.")
                ->show();
        } finally {
            $this->downloadingTheme = '';
        }
    }

    public function render(): View
    {
        return view('livewire.vscode-theme-browser');
    }
}
