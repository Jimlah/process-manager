<?php

namespace Database\Seeders;

use App\Services\ThemeService;
use Illuminate\Database\Seeder;

class ThemeSeeder extends Seeder
{
    public function run(): void
    {
        ThemeService::seedBuiltinThemes();
    }
}
