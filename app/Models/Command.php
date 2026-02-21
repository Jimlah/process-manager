<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Command extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'name',
        'command',
        'alias',
        'status',
        'auto_start',
        'auto_restart',
    ];

    protected function casts(): array
    {
        return [
            'auto_start' => 'boolean',
            'auto_restart' => 'boolean',
        ];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function processLog(): HasOne
    {
        return $this->hasOne(ProcessLog::class);
    }
}
