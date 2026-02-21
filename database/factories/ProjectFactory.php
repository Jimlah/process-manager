<?php

namespace Database\Factories;

use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProjectFactory extends Factory
{
    protected $model = Project::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->words(2, true),
            'path' => '/Users/'.get_current_user().'/Projects/'.$this->faker->slug(),
        ];
    }
}
