<?php

use Laravel\Mcp\Facades\Mcp;

Mcp::local('agent', \App\Mcp\Servers\LocalAgentServer::class);
