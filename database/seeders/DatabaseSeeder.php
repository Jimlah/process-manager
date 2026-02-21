<?php

namespace Database\Seeders;

use App\Models\Command;
use App\Models\Project;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create sample project 1: eClinic
        $eclinic = Project::create([
            'name' => 'eClinic',
            'path' => '/Users/'.get_current_user().'/Projects/eclinic',
        ]);

        Command::create([
            'project_id' => $eclinic->id,
            'name' => 'Laravel Server',
            'command' => 'php artisan serve',
            'alias' => 'eclinic-laravel-'.uniqid(),
            'status' => 'stopped',
        ]);

        Command::create([
            'project_id' => $eclinic->id,
            'name' => 'Vite Dev Server',
            'command' => 'npm run dev',
            'alias' => 'eclinic-vite-'.uniqid(),
            'status' => 'stopped',
        ]);

        Command::create([
            'project_id' => $eclinic->id,
            'name' => 'Queue Worker',
            'command' => 'php artisan queue:work',
            'alias' => 'eclinic-queue-'.uniqid(),
            'status' => 'stopped',
        ]);

        // Create sample project 2: AI Models
        $aiModels = Project::create([
            'name' => 'AI Models',
            'path' => '/Users/'.get_current_user().'/Projects/ai-models',
        ]);

        Command::create([
            'project_id' => $aiModels->id,
            'name' => 'Ollama Server',
            'command' => 'ollama serve',
            'alias' => 'ai-ollama-'.uniqid(),
            'status' => 'stopped',
        ]);

        // Create sample project 3: Timeline Editor
        $timeline = Project::create([
            'name' => 'Timeline Editor',
            'path' => '/Users/'.get_current_user().'/Projects/timeline-editor',
        ]);

        Command::create([
            'project_id' => $timeline->id,
            'name' => 'Vite Dev',
            'command' => 'npm run dev',
            'alias' => 'timeline-vite-'.uniqid(),
            'status' => 'stopped',
        ]);
    }
}
