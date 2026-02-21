<?php

namespace App\Mcp\Servers;

use Laravel\Mcp\Server;
use Laravel\Mcp\Server\Attributes\Instructions;
use Laravel\Mcp\Server\Attributes\Name;
use Laravel\Mcp\Server\Attributes\Version;

#[Name('Local Agent Server')]
#[Version('0.0.1')]
#[Instructions('Instructions describing how to use the server and its features.')]
class LocalAgentServer extends Server
{
    protected array $tools = [
        \App\Mcp\Tools\ReadLogsTool::class,
        \App\Mcp\Tools\ListCommandsTool::class,
    ];

    protected array $resources = [
        //
    ];

    protected array $prompts = [
        //
    ];
}
