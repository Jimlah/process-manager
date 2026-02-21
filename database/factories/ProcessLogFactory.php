<?php

namespace Database\Factories;

use App\Models\Command;
use App\Models\ProcessLog;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProcessLogFactory extends Factory
{
    protected $model = ProcessLog::class;

    public function definition(): array
    {
        return [
            'command_id' => Command::factory(),
            'content' => $this->faker->paragraphs(3, true),
        ];
    }
}
