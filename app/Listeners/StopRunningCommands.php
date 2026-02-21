<?php

namespace App\Listeners;

use App\Models\Command;
use Native\Desktop\Events\Windows\WindowClosed;

class StopRunningCommands
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(WindowClosed $event): void
    {
        Command::where('status', 'running')->update(['status' => 'stopped']);
    }
}
