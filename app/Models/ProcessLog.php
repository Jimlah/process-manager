<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProcessLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'command_id',
        'content',
    ];

    public function command(): BelongsTo
    {
        return $this->belongsTo(Command::class);
    }
}
