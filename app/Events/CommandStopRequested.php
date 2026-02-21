<?php

namespace App\Events;

use App\Models\Command;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CommandStopRequested
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
