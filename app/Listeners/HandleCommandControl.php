<?php

namespace App\Listeners;

use App\Events\CommandStartRequested;
use App\Events\CommandStopRequested;
use Native\Desktop\Facades\ChildProcess;
use Native\Desktop\Facades\Notification;

class HandleCommandControl
{
    /**
     * Handle the event.
     */
    public function handle(object $event): void
    {
        if ($event instanceof CommandStartRequested) {
            $this->handleStart($event);
        } elseif ($event instanceof CommandStopRequested) {
            $this->handleStop($event);
        }
    }

    protected function handleStart(CommandStartRequested $event): void
    {
        $command = $event->command;

        $command->processLog?->update(['content' => '']);

        ChildProcess::start(
            cmd: $command->command,
            alias: $command->alias,
            cwd: $command->project->path,
            env: [],
        );

        $command->update(['status' => 'running']);

        Notification::title('Process Started')
            ->message("Started '{$command->name}' in {$command->project->name}.")
            ->show();

        \App\Events\ProcessStatusChanged::dispatch($command);
    }

    protected function handleStop(CommandStopRequested $event): void
    {
        $command = $event->command;
        $alias = $command->alias;

        $command->update(['status' => 'stopped']);

        ChildProcess::stop($alias);

        // Retry to ensure it's dead
        usleep(500_000);

        if (ChildProcess::get($alias) !== null) {
            ChildProcess::stop($alias);
        }

        Notification::title('Process Stopped')
            ->message("Stopped '{$command->name}'.")
            ->show();

        \App\Events\ProcessStatusChanged::dispatch($command);
    }
}
