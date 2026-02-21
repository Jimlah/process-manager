<?php

namespace App\Mcp\Tools;

use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;

#[Description('Reads the stdout/stderr logs of a specific managed child process (command) by its ID.')]
class ReadLogsTool extends Tool
{
    /**
     * Handle the tool request.
     */
    public function handle(Request $request): Response
    {
        $commandId = $request->input('command_id');

        if (! $commandId) {
            return Response::text('Command ID is required.');
        }

        $command = \App\Models\Command::with('processLog')->find($commandId);

        if (! $command) {
            return Response::text("Command with ID {$commandId} not found.");
        }

        if (! $command->processLog) {
            return Response::text("No logs found for Command ID {$commandId}.");
        }

        return Response::text($command->processLog->content);
    }

    /**
     * Get the tool's input schema.
     *
     * @return array<string, \Illuminate\Contracts\JsonSchema\JsonSchema>
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'command_id' => $schema->integer()
                ->description('The ID of the command/process to read logs for.')
                ->required(),
        ];
    }
}
