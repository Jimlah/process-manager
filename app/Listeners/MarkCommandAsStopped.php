<?php

namespace App\Listeners;

use App\Models\Command;
use Native\Desktop\Events\ChildProcess\ProcessExited;
use Native\Desktop\Facades\Notification;

class MarkCommandAsStopped
{
    /**
     * Handle the event.
     */
    public function handle(ProcessExited $event): void
    {
        $command = Command::where('alias', $event->alias)->first();

        if ($command) {
            $command->update(['status' => 'stopped']);

            Notification::title('Process Exited')
                ->message("The process '{$command->name}' has exited (code: {$event->code}).")
                ->show();

            if ($command->auto_restart) {
                \App\Events\CommandStartRequested::dispatch($command);
            }
        }
    }
}
