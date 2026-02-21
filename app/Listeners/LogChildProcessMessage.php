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

        $html = $this->convertAnsiToHtml($event->data);

        $processLog = $command->processLog;

        if ($processLog) {
            $processLog->update([
                'content' => $processLog->content.$html,
            ]);
        } else {
            $command->processLog()->create([
                'content' => $html,
            ]);
        }
    }

    private function convertAnsiToHtml(string $text): string
    {
        $converter = new \SensioLabs\AnsiConverter\AnsiToHtmlConverter();
        
        return $converter->convert($text);
    }
}
