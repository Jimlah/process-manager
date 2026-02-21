<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Theme extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'colors',
        'token_colors',
        'preview_url',
        'is_active',
        'is_builtin',
    ];

    protected function casts(): array
    {
        return [
            'colors' => 'array',
            'token_colors' => 'array',
            'is_active' => 'boolean',
            'is_builtin' => 'boolean',
        ];
    }

    public static function getActive(): ?self
    {
        return self::where('is_active', true)->first();
    }

    public function activate(): void
    {
        self::where('is_active', true)->update(['is_active' => false]);
        $this->update(['is_active' => true]);
    }
}
