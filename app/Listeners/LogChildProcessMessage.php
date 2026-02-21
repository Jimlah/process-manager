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

        $clean = $this->stripAnsiCodes($event->data);

        $processLog = $command->processLog;

        if ($processLog) {
            $processLog->update([
                'content' => $processLog->content.$clean,
            ]);
        } else {
            $command->processLog()->create([
                'content' => $clean,
            ]);
        }
    }

    private function stripAnsiCodes(string $text): string
    {
        // Strip CSI sequences (colors, cursor, erase) and OSC sequences (terminal titles)
        return preg_replace([
            '/\e\[[0-9;]*[A-Za-z]/',
            '/\e\][^\a]*(?:\a|\e\\\\)/',
        ], '', $text);
    }
}
