<?php

namespace App\Services;

use App\Models\Theme;
use Illuminate\Support\Facades\File;

class ThemeService
{
    private const CSS_PATH = 'css/theme.css';

    /**
     * Map VS Code theme colors to our application CSS variables
     */
    public static function mapVsCodeColors(array $colors): array
    {
        return [
            'background' => $colors['editor.background'] ?? $colors['sideBar.background'] ?? '#1e1e2e',
            'foreground' => $colors['editor.foreground'] ?? '#cdd6f4',
            'card' => $colors['sideBar.background'] ?? $colors['activityBar.background'] ?? '#181825',
            'card-foreground' => $colors['sideBar.foreground'] ?? $colors['activityBar.foreground'] ?? '#cdd6f4',
            'popover' => $colors['dropdown.background'] ?? $colors['sideBar.background'] ?? '#181825',
            'popover-foreground' => $colors['dropdown.foreground'] ?? '#cdd6f4',
            'primary' => $colors['button.background'] ?? $colors['activityBarBadge.background'] ?? '#cba6f7',
            'primary-foreground' => $colors['button.foreground'] ?? '#11111b',
            'secondary' => $colors['input.background'] ?? $colors['sideBarSectionHeader.background'] ?? '#313244',
            'secondary-foreground' => $colors['input.foreground'] ?? '#cdd6f4',
            'muted' => $colors['badge.background'] ?? $colors['list.inactiveSelectionBackground'] ?? '#45475a',
            'muted-foreground' => $colors['badge.foreground'] ?? $colors['descriptionForeground'] ?? '#a6adc8',
            'accent' => $colors['list.activeSelectionBackground'] ?? $colors['focusBorder'] ?? '#313244',
            'accent-foreground' => $colors['list.activeSelectionForeground'] ?? '#cdd6f4',
            'destructive' => $colors['errorForeground'] ?? '#f38ba8',
            'destructive-foreground' => $colors['editor.background'] ?? '#11111b',
            'success' => $colors['terminal.ansiGreen'] ?? '#a6e3a1',
            'success-foreground' => $colors['editor.background'] ?? '#11111b',
            'border' => $colors['panel.border'] ?? $colors['sideBar.border'] ?? '#313244',
            'input' => $colors['input.background'] ?? '#313244',
            'ring' => $colors['focusBorder'] ?? $colors['button.background'] ?? '#cba6f7',
            'terminal' => $colors['terminal.background'] ?? $colors['editor.background'] ?? '#000000',
            'terminal-text' => $colors['terminal.foreground'] ?? $colors['editor.foreground'] ?? '#cba6f7',
        ];
    }

    /**
     * Generate CSS custom properties from theme
     */
    public static function generateCss(?Theme $theme = null): string
    {
        if (! $theme) {
            $theme = Theme::getActive();
        }

        if (! $theme) {
            return '';
        }

        $mapped = self::mapVsCodeColors($theme->colors);
        $css = "/* Custom Theme: {$theme->name} */\n";
        $css .= ":root {\n";

        foreach ($mapped as $key => $value) {
            $css .= "  --theme-{$key}: {$value};\n";
        }

        $css .= "}\n";

        return $css;
    }

    /**
     * Write the active theme CSS to the public file
     */
    public static function writeThemeFile(): string
    {
        $css = self::generateCss();
        $path = public_path(self::CSS_PATH);

        // Ensure directory exists
        $dir = dirname($path);
        if (! File::exists($dir)) {
            File::makeDirectory($dir, 0755, true);
        }

        File::put($path, $css);

        return self::CSS_PATH;
    }

    /**
     * Get the theme file URL with cache-busting timestamp
     */
    public static function getThemeUrl(): ?string
    {
        $path = public_path(self::CSS_PATH);

        if (! File::exists($path)) {
            self::writeThemeFile();
        }

        $timestamp = File::lastModified($path);

        return asset(self::CSS_PATH).'?v='.$timestamp;
    }

    /**
     * Check if theme file exists
     */
    public static function themeFileExists(): bool
    {
        return File::exists(public_path(self::CSS_PATH));
    }

    /**
     * Seed built-in themes
     */
    public static function seedBuiltinThemes(): void
    {
        $themes = [
            [
                'name' => 'Dark (Catppuccin Mocha)',
                'slug' => 'dark',
                'colors' => [
                    'editor.background' => '#1e1e2e',
                    'editor.foreground' => '#cdd6f4',
                    'sideBar.background' => '#181825',
                    'activityBar.background' => '#181825',
                    'button.background' => '#cba6f7',
                    'input.background' => '#313244',
                    'badge.background' => '#45475a',
                    'list.activeSelectionBackground' => '#313244',
                    'panel.border' => '#313244',
                    'focusBorder' => '#cba6f7',
                    'terminal.background' => '#000000',
                    'terminal.foreground' => '#cba6f7',
                ],
                'is_active' => true,
                'is_builtin' => true,
            ],
            [
                'name' => 'Light (Catppuccin Latte)',
                'slug' => 'light',
                'colors' => [
                    'editor.background' => '#eff1f5',
                    'editor.foreground' => '#4c4f69',
                    'sideBar.background' => '#e6e9ef',
                    'activityBar.background' => '#e6e9ef',
                    'button.background' => '#8839ef',
                    'input.background' => '#ccd0da',
                    'badge.background' => '#bcc0cc',
                    'list.activeSelectionBackground' => '#ccd0da',
                    'panel.border' => '#bcc0cc',
                    'focusBorder' => '#8839ef',
                    'terminal.background' => '#dce0e8',
                    'terminal.foreground' => '#7c3aed',
                ],
                'is_active' => false,
                'is_builtin' => true,
            ],
        ];

        foreach ($themes as $themeData) {
            Theme::firstOrCreate(
                ['slug' => $themeData['slug']],
                $themeData
            );
        }
    }
}
