<?php

namespace App\Events;

use App\Models\Command;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CommandStartRequested
{
    use Dispatchable, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(public Command $command)
    {
        //
    }
}
