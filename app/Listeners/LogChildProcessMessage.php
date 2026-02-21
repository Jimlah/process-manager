<?php

namespace App\Listeners;

use App\Models\Command;
use Native\Desktop\Events\ChildProcess\MessageReceived;

class LogChildProcessMessage
{
    /**
     * Handle the event.
     */
    public function handle(MessageReceived $event): void
    {
        $command = Command::where('alias', $event->alias)->first();

        if (! $command || $command->status !== 'running') {
            return;
        }

        $processLog = $command->processLog;

        $data = str_ends_with($event->data, "\n") ? $event->data : $event->data."\n";

        if ($processLog) {
            $processLog->update([
                'content' => $processLog->content.$data,
            ]);
        } else {
            $command->processLog()->create([
                'content' => $data,
            ]);
        }
    }
}
