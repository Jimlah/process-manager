<?php

namespace App\Livewire;

use App\Models\Theme;
use App\Services\ThemeService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;
use Livewire\Component;
use Native\Desktop\Dialog;
use Native\Desktop\Facades\Notification;

class Settings extends Component
{
    public string $activeTab = 'themes';

    public string $themeJson = '';

    public ?int $selectedThemeId = null;

    public string $importStatus = '';

    public string $previewUrl = '';

    protected $listeners = ['theme-downloaded' => 'refreshThemes'];

    public function mount(): void
    {
        $activeTheme = Theme::getActive();
        $this->selectedThemeId = $activeTheme?->id;
    }

    public function loadThemeFromFile(): void
    {
        $path = Dialog::new()
            ->files()
            ->allowExtensions(['json'])
            ->open();

        if (! $path) {
            return;
        }

        $content = file_get_contents($path);
        if ($content === false) {
            $this->importStatus = 'Failed to read file';

            return;
        }

        $this->themeJson = $content;
        $this->importStatus = 'File loaded. Click Import to apply.';
    }

    public function importTheme(): void
    {
        $this->validate([
            'themeJson' => 'required|json',
        ]);

        $themeData = json_decode($this->themeJson, true);

        if (! isset($themeData['name']) || ! isset($themeData['colors'])) {
            $this->importStatus = 'Invalid VS Code theme format. Missing name or colors.';

            return;
        }

        $name = $themeData['name'];
        $slug = Str::slug($name).'-'.uniqid();

        // Check if theme already exists by name (slug has uniqid suffix so can't match on it)
        $existing = Theme::where('name', $name)->first();
        if ($existing) {
            $existing->update([
                'colors' => $themeData['colors'],
                'token_colors' => $themeData['tokenColors'] ?? null,
                'preview_url' => $this->previewUrl ?: $existing->preview_url,
            ]);
            $existing->activate();
            $this->selectedThemeId = $existing->id;
        } else {
            $theme = Theme::create([
                'name' => $name,
                'slug' => $slug,
                'colors' => $themeData['colors'],
                'token_colors' => $themeData['tokenColors'] ?? null,
                'preview_url' => $this->previewUrl ?: null,
                'is_active' => true,
                'is_builtin' => false,
            ]);
            $this->selectedThemeId = $theme->id;
        }

        // Regenerate theme CSS file
        ThemeService::writeThemeFile();

        $this->themeJson = '';
        $this->previewUrl = '';
        $this->importStatus = "Theme '{$name}' imported successfully!";
        
        Notification::title('Theme Imported')
            ->message("The theme '{$name}' was successfully imported.")
            ->show();

        // Refresh page to apply CSS
        $this->redirect(route('settings'));
    }

    public function activateTheme(int $themeId): void
    {
        $theme = Theme::query()->find($themeId);
        if (! $theme) {
            return;
        }

        $theme->activate();
        $this->selectedThemeId = $themeId;

        // Regenerate theme CSS file
        ThemeService::writeThemeFile();

        $this->importStatus = "Theme '{$theme->name}' activated!";

        Notification::title('Theme Activated')
            ->message("The theme '{$theme->name}' is now active.")
            ->show();

        // Refresh page to apply CSS
        $this->redirect(route('settings'));
    }

    public function deleteTheme(int $themeId): void
    {
        $theme = Theme::query()->find($themeId);
        if (! $theme || $theme->is_builtin) {
            return;
        }

        $theme->delete();

        if ($this->selectedThemeId === $themeId) {
            $this->selectedThemeId = null;
            // Activate default dark theme
            $default = Theme::query()->where('slug', 'dark')->first();
            if ($default) {
                $default->activate();
                $this->selectedThemeId = $default->id;
            }
        }

        // Regenerate theme CSS file
        ThemeService::writeThemeFile();

        $this->importStatus = 'Theme deleted.';

        Notification::title('Theme Deleted')
            ->message("The theme '{$theme->name}' has been deleted.")
            ->show();

        // Refresh page to apply CSS
        $this->redirect(route('settings'));
    }

    public function setTab(string $tab): void
    {
        $this->activeTab = $tab;
    }

    public function refreshThemes(): void
    {
        $this->selectedThemeId = Theme::getActive()?->id;
    }

    public function render(): View
    {
        return view('livewire.settings', [
            'themes' => Theme::orderBy('name')->get(),
        ]);
    }
}
