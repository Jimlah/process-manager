<?php

namespace App\Services;

use App\Models\Theme;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use ZipArchive;

class VSCodeThemeService
{
    private const MARKETPLACE_API_URL = 'https://marketplace.visualstudio.com/_apis/public/gallery/extensionquery?api-version=3.0-preview.1';

    private const DOWNLOAD_URL_TEMPLATE = 'https://marketplace.visualstudio.com/_apis/public/gallery/publishers/{publisher}/vsextensions/{extension}/{version}/vspackage';

    /**
     * Popular VS Code theme extensions to download
     */
    private const POPULAR_THEMES = [
        ['publisher' => 'GitHub', 'extension' => 'github-vscode-theme', 'name' => 'GitHub Theme'],
        ['publisher' => 'zhuangtongfa', 'extension' => 'Material-theme', 'name' => 'One Dark Pro'],
        ['publisher' => 'dracula-theme', 'extension' => 'theme-dracula', 'name' => 'Dracula Official'],
        ['publisher' => 'akamud', 'extension' => 'vscode-theme-onedark', 'name' => 'Atom One Dark'],
        ['publisher' => 'sdras', 'extension' => 'night-owl', 'name' => 'Night Owl'],
        ['publisher' => 'johnpapa', 'extension' => 'winteriscoming', 'name' => 'Winter is Coming'],
        ['publisher' => 'monokai', 'extension' => 'theme-monokai-pro-vscode', 'name' => 'Monokai Pro'],
        ['publisher' => 'enkia', 'extension' => 'tokyo-night', 'name' => 'Tokyo Night'],
        ['publisher' => 'whizkydee', 'extension' => 'material-palenight-theme', 'name' => 'Palenight Theme'],
        ['publisher' => 'RobbOwen', 'extension' => 'synthwave-vscode', 'name' => 'SynthWave \'84'],
        ['publisher' => 'ahmadawais', 'extension' => 'shades-of-purple', 'name' => 'Shades of Purple'],
        ['publisher' => 'wesbos', 'extension' => 'theme-cobalt2', 'name' => 'Cobalt2'],
        ['publisher' => 'EliverLara', 'extension' => 'andromeda', 'name' => 'Andromeda'],
        ['publisher' => 'liviuschera', 'extension' => 'noctis', 'name' => 'Noctis'],
        ['publisher' => 'tinkertrain', 'extension' => 'panda-syntax-vscode', 'name' => 'Panda Theme'],
        ['publisher' => 'arcticicestudio', 'extension' => 'nord-visual-studio-code', 'name' => 'Nord'],
        ['publisher' => 'Catppuccin', 'extension' => 'catppuccin-vsc', 'name' => 'Catppuccin'],
        ['publisher' => 'rocketseat', 'extension' => 'omni', 'name' => 'Omni Theme'],
        ['publisher' => 'jdinhlife', 'extension' => 'gruvbox', 'name' => 'Gruvbox Theme'],
        ['publisher' => 'rokoroku', 'extension' => 'dark-plus-dracula', 'name' => 'Darcula Theme'],
        ['publisher' => 'PawelBorkar', 'extension' => 'jellyfish', 'name' => 'JellyFish Theme'],
        ['publisher' => 'fisheva', 'extension' => 'eva-theme', 'name' => 'Eva Theme'],
        ['publisher' => 'mariusradvan', 'extension' => 'vue-theme', 'name' => 'Vue Theme'],
        ['publisher' => 'shopify', 'extension' => 'ruby-lsp', 'name' => 'Ruby Theme'],
        ['publisher' => 'Fabiospampinato', 'extension' => 'monokai-night', 'name' => 'Monokai Night'],
        ['publisher' => 'unthrottled', 'extension' => 'doki-theme', 'name' => 'Doki Theme'],
        ['publisher' => 'miguelsolorio', 'extension' => 'min-theme', 'name' => 'Min Theme'],
        ['publisher' => 'benjaminbenais', 'extension' => 'copilot-theme', 'name' => 'Copilot Theme'],
        ['publisher' => 'teabyii', 'extension' => 'ayu', 'name' => 'Ayu'],
        ['publisher' => 'azemoh', 'extension' => 'one-monokai', 'name' => 'One Monokai'],
        ['publisher' => 'akamud', 'extension' => 'vscode-theme-onelight', 'name' => 'Atom One Light'],
        ['publisher' => 'BeardedBear', 'extension' => 'beardedtheme', 'name' => 'Bearded Theme'],
        ['publisher' => 'mhartington', 'extension' => 'oceanic-next', 'name' => 'Oceanic Next'],
        ['publisher' => 'Equinusocio', 'extension' => 'vsc-material-theme', 'name' => 'Material Theme'],
        ['publisher' => 'MS-CEINTL', 'extension' => 'vscode-language-pack-en-GB', 'name' => 'C/C++ Themes'],
    ];

    /**
     * Get all popular themes
     *
     * @return array<int, array{publisher: string, extension: string, name: string}>
     */
    public function getPopularThemes(): array
    {
        return self::POPULAR_THEMES;
    }

    /**
     * Search themes via vscodethemes.com API
     *
     * @return array{total: int, extensions: array<int, array{name: string, displayName: string, publisherName: string, shortDescription: string, themes: array, totalThemes: int}>}
     */
    public function searchVSCodeThemes(string $query = '', int $page = 1, int $pageSize = 36): array
    {
        $params = [
            '_data' => 'routes/_index',
            'language' => 'js',
            'extensionsPageNumber' => $page,
            'extensionsPageSize' => $pageSize,
            'themesPageNumber' => 1,
            'themesPageSize' => 10,
            'sortBy' => 'installs',
        ];

        if ($query !== '') {
            $params['text'] = $query;
        }

        $response = Http::get('https://vscodethemes.com/', $params);

        if (! $response->successful()) {
            throw new \RuntimeException('Failed to search vscodethemes.com: '.$response->body());
        }

        $data = $response->json();

        return [
            'total' => $data['results']['total'] ?? 0,
            'extensions' => $data['results']['extensions'] ?? [],
        ];
    }

    /**
     * Get detailed theme data from vscodethemes.com
     *
     * @return array{name: string, displayName: string, publisherName: string, theme: array}
     */
    public function getExtensionThemeDetail(string $publisherName, string $extensionName, string $themeName): array
    {
        $slug = "{$publisherName}.{$extensionName}";

        $response = Http::get("https://vscodethemes.com/e/{$slug}/{$themeName}", [
            '_data' => 'routes/e.$slug.$theme',
        ]);

        if (! $response->successful()) {
            throw new \RuntimeException('Failed to get theme detail: '.$response->body());
        }

        $data = $response->json();
        $extension = $data['results']['extensions'][0] ?? null;

        if (! $extension) {
            throw new \RuntimeException('Theme extension not found');
        }

        return $extension;
    }

    /**
     * Download and import all popular themes
     *
     * @return array<int, array{success: bool, theme: string, message: string}>
     */
    public function downloadAllPopularThemes(): array
    {
        $results = [];

        foreach (self::POPULAR_THEMES as $themeInfo) {
            try {
                $theme = $this->downloadAndImportTheme(
                    $themeInfo['publisher'],
                    $themeInfo['extension']
                );
                $results[] = [
                    'success' => true,
                    'theme' => $theme->name,
                    'message' => 'Imported successfully',
                ];
            } catch (\Throwable $e) {
                Log::error("Failed to import theme {$themeInfo['name']}: {$e->getMessage()}");
                $results[] = [
                    'success' => false,
                    'theme' => $themeInfo['name'],
                    'message' => $e->getMessage(),
                ];
            }
        }

        return $results;
    }

    /**
     * Search for themes in the VS Code Marketplace
     *
     * @return array<int, array{name: string, publisher: string, extension: string, version: string, displayName: string, description: string}>
     */
    public function searchThemes(string $query, int $pageSize = 20): array
    {
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json;api-version=3.0-preview.1',
        ])->post(self::MARKETPLACE_API_URL, [
            'filters' => [
                [
                    'criteria' => [
                        ['filterType' => 8, 'value' => 'Microsoft.VisualStudio.Code'],
                        ['filterType' => 10, 'value' => $query],
                        ['filterType' => 12, 'value' => '37888'],
                    ],
                    'pageNumber' => 1,
                    'pageSize' => $pageSize,
                    'sortBy' => 0,
                    'sortOrder' => 0,
                ],
            ],
            'assetTypes' => [],
            'flags' => 0x1 | 0x2 | 0x80,
        ]);

        if (! $response->successful()) {
            throw new \RuntimeException('Failed to search themes: '.$response->body());
        }

        $data = $response->json();
        $themes = [];

        foreach ($data['results'][0]['extensions'] ?? [] as $extension) {
            $themes[] = [
                'name' => $extension['extensionName'],
                'publisher' => $extension['publisher']['publisherName'],
                'extension' => $extension['extensionName'],
                'version' => $this->getLatestVersion($extension),
                'displayName' => $extension['displayName'] ?? $extension['extensionName'],
                'description' => $extension['shortDescription'] ?? '',
                'themes' => $this->extractThemesFromExtension($extension),
            ];
        }

        return $themes;
    }

    /**
     * Download and import a theme from the VS Code Marketplace
     */
    public function downloadAndImportTheme(string $publisher, string $extension, ?string $themeName = null, ?string $previewUrl = null): Theme
    {
        $extensionData = $this->getExtensionData($publisher, $extension);
        $version = $this->getLatestVersion($extensionData);

        $downloadUrl = str_replace(
            ['{publisher}', '{extension}', '{version}'],
            [$publisher, $extension, $version],
            self::DOWNLOAD_URL_TEMPLATE
        );

        $tempDir = storage_path('app/temp/themes');
        if (! is_dir($tempDir)) {
            mkdir($tempDir, 0755, true);
        }

        $vsixPath = $tempDir."/{$publisher}.{$extension}.{$version}.vsix";

        // Download the .vsix file
        $response = Http::get($downloadUrl);
        if (! $response->successful()) {
            throw new \RuntimeException("Failed to download theme: {$response->body()}");
        }

        file_put_contents($vsixPath, $response->body());

        try {
            // Extract theme JSON from .vsix
            $themeData = $this->extractThemeFromVsix($vsixPath, $themeName);

            // Import the theme
            $theme = $this->importTheme($themeData, $previewUrl);

            return $theme;
        } finally {
            // Cleanup
            if (file_exists($vsixPath)) {
                unlink($vsixPath);
            }
        }
    }

    /**
     * Get extension data from marketplace
     */
    private function getExtensionData(string $publisher, string $extension): array
    {
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json;api-version=3.0-preview.1',
        ])->post(self::MARKETPLACE_API_URL, [
            'filters' => [
                [
                    'criteria' => [
                        ['filterType' => 7, 'value' => "{$publisher}.{$extension}"],
                    ],
                    'pageNumber' => 1,
                    'pageSize' => 1,
                ],
            ],
            'assetTypes' => [],
            'flags' => 0x1 | 0x2 | 0x80,
        ]);

        if (! $response->successful()) {
            throw new \RuntimeException('Failed to get extension data: '.$response->body());
        }

        $data = $response->json();

        return $data['results'][0]['extensions'][0] ?? throw new \RuntimeException('Extension not found');
    }

    /**
     * Extract theme files from a .vsix archive
     */
    private function extractThemeFromVsix(string $vsixPath, ?string $themeName = null): array
    {
        $zip = new ZipArchive;
        if ($zip->open($vsixPath) !== true) {
            throw new \RuntimeException('Failed to open .vsix file');
        }

        try {
            // Read package.json to find theme contributions
            $packageJsonContent = $zip->getFromName('extension/package.json');
            if ($packageJsonContent === false) {
                throw new \RuntimeException('Could not find package.json in extension');
            }

            $packageJson = json_decode($packageJsonContent, true);
            if (! isset($packageJson['contributes']['themes'])) {
                throw new \RuntimeException('No themes found in this extension');
            }

            $themes = $packageJson['contributes']['themes'];

            // If specific theme name requested, find it
            if ($themeName) {
                $targetTheme = null;
                foreach ($themes as $theme) {
                    if ($theme['label'] === $themeName || $theme['id'] === $themeName) {
                        $targetTheme = $theme;

                        break;
                    }
                }
                if (! $targetTheme) {
                    throw new \RuntimeException("Theme '{$themeName}' not found in extension");
                }
                $themes = [$targetTheme];
            }

            // For now, just use the first theme
            $theme = $themes[0];
            $themePath = 'extension/'.ltrim($theme['path'], './');

            $themeContent = $zip->getFromName($themePath);
            if ($themeContent === false) {
                throw new \RuntimeException("Could not read theme file: {$themePath}");
            }

            $themeData = json_decode($themeContent, true);
            if (! $themeData) {
                throw new \RuntimeException('Invalid theme JSON');
            }

            return $themeData;
        } finally {
            $zip->close();
        }
    }

    /**
     * Import a theme into the database
     */
    private function importTheme(array $themeData, ?string $previewUrl = null): Theme
    {
        if (! isset($themeData['name']) || ! isset($themeData['colors'])) {
            throw new \RuntimeException('Invalid VS Code theme format. Missing name or colors.');
        }

        $name = $themeData['name'];
        $slug = Str::slug($name);

        // Check if theme already exists
        $existing = Theme::where('slug', $slug)->first();
        if ($existing) {
            $existing->update([
                'colors' => $themeData['colors'],
                'token_colors' => $themeData['tokenColors'] ?? null,
                'preview_url' => $previewUrl ?? $existing->preview_url,
            ]);
            $existing->activate();

            ThemeService::writeThemeFile();

            return $existing;
        }

        $theme = Theme::create([
            'name' => $name,
            'slug' => $slug.'-'.uniqid(),
            'colors' => $themeData['colors'],
            'token_colors' => $themeData['tokenColors'] ?? null,
            'preview_url' => $previewUrl,
            'is_active' => true,
            'is_builtin' => false,
        ]);

        ThemeService::writeThemeFile();

        return $theme;
    }

    /**
     * Get the latest version from extension data
     */
    private function getLatestVersion(array $extension): string
    {
        $versions = $extension['versions'] ?? [];
        if (empty($versions)) {
            throw new \RuntimeException('No versions found for extension');
        }

        // Sort by lastUpdated to get latest
        usort($versions, function ($a, $b) {
            return strtotime($b['lastUpdated'] ?? 'now') <=> strtotime($a['lastUpdated'] ?? 'now');
        });

        return $versions[0]['version'];
    }

    /**
     * Extract theme information from extension data
     *
     * @return array<int, array{label: string, path: string}>
     */
    private function extractThemesFromExtension(array $extension): array
    {
        $themes = [];

        // Try to get from version properties
        foreach ($extension['versions'] ?? [] as $version) {
            foreach ($version['files'] ?? [] as $file) {
                if ($file['assetType'] === 'Microsoft.VisualStudio.Services.Content.Details') {
                    // This would contain the package.json content
                    // For now, we'll return empty and get themes during download
                    break;
                }
            }
        }

        return $themes;
    }
}
