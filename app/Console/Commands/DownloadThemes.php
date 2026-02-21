<?php

namespace App\Console\Commands;

use App\Services\VSCodeThemeService;
use Illuminate\Console\Command;

class DownloadThemes extends Command
{
    protected $signature = 'themes:download
                            {--publisher= : Download specific theme by publisher}
                            {--extension= : Download specific theme by extension name}
                            {--all : Download all popular themes}';

    protected $description = 'Download VS Code themes from the marketplace';

    public function handle(VSCodeThemeService $service): int
    {
        if ($this->option('all')) {
            return $this->downloadAllThemes($service);
        }

        if ($this->option('publisher') && $this->option('extension')) {
            return $this->downloadSpecificTheme(
                $service,
                $this->option('publisher'),
                $this->option('extension')
            );
        }

        $this->error('Please specify either --all to download all themes, or both --publisher and --extension for a specific theme');

        return 1;
    }

    private function downloadAllThemes(VSCodeThemeService $service): int
    {
        $this->info('Starting download of all popular VS Code themes...');
        $this->newLine();

        $themes = $service->getPopularThemes();
        $this->info('Found '.count($themes).' themes to download');
        $this->newLine();

        $results = $service->downloadAllPopularThemes();

        $successCount = 0;
        $failCount = 0;

        foreach ($results as $result) {
            if ($result['success']) {
                $this->info("✓ {$result['theme']}: {$result['message']}");
                $successCount++;
            } else {
                $this->error("✗ {$result['theme']}: {$result['message']}");
                $failCount++;
            }
        }

        $this->newLine();
        $this->info('Download complete!');
        $this->info("Successful: {$successCount}");
        $this->warn("Failed: {$failCount}");

        return 0;
    }

    private function downloadSpecificTheme(VSCodeThemeService $service, string $publisher, string $extension): int
    {
        $this->info("Downloading theme: {$publisher}.{$extension}...");

        try {
            $theme = $service->downloadAndImportTheme($publisher, $extension);
            $this->info("✓ Successfully downloaded and imported: {$theme->name}");

            return 0;
        } catch (\Throwable $e) {
            $this->error("✗ Failed to download theme: {$e->getMessage()}");

            return 1;
        }
    }
}
