<?php

namespace App\Mcp\Tools;

use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;

#[Description('Lists all configured commands/processes and their IDs. Use this to find a command_id to read logs for.')]
class ListCommandsTool extends Tool
{
    /**
     * Handle the tool request.
     */
    public function handle(Request $request): Response
    {
        $commands = \App\Models\Command::with('project')->get();

        if ($commands->isEmpty()) {
            return Response::text('No commands are currently configured.');
        }

        $output = "Available Commands/Processes:\n\n";

        foreach ($commands as $command) {
            $projectName = $command->project ? $command->project->name : 'Unknown Project';
            $output .= "- ID: {$command->id}\n";
            $output .= "  Name: {$command->name}\n";
            $output .= "  Project: {$projectName}\n";
            $output .= "  Command: {$command->command}\n";
            $output .= "  Status: {$command->status}\n";
            $output .= "\n";
        }

        return Response::text($output);
    }

    /**
     * Get the tool's input schema.
     *
     * @return array<string, \Illuminate\Contracts\JsonSchema\JsonSchema>
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            // No arguments required
        ];
    }
}
