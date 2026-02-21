<?php

namespace Database\Factories;

use App\Models\Command;
use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class CommandFactory extends Factory
{
    protected $model = Command::class;

    public function definition(): array
    {
        $commands = [
            'php artisan serve',
            'npm run dev',
            'php artisan queue:work',
            'php artisan horizon',
            'composer install',
            'npm install',
        ];

        $names = [
            'Laravel Server',
            'Vite Dev Server',
            'Queue Worker',
            'Horizon',
            'Composer Install',
            'NPM Install',
        ];

        $index = array_rand($commands);
        $name = $names[$index];
        $alias = Str::slug($name).'-'.$this->faker->unique()->randomNumber(4);

        return [
            'project_id' => Project::factory(),
            'name' => $name,
            'command' => $commands[$index],
            'alias' => $alias,
            'status' => $this->faker->randomElement(['stopped', 'running']),
        ];
    }
}
