<?php

namespace Basanta\LaravelWebsocket\Commands;

class StartWebsocketServerCommand extends \Illuminate\Console\Command
{
    protected $signature = 'websocket:serve';
    protected $description = 'Start the WebSocket server';

    public function handle()
    {
        $port = config('websocket.port');
        $this->info('Running WebSocket server on port ' . $port);
        $node = `node -v`;
        if(version_compare($node, 'v16.0.0', '<')) {
            $this->error('Node.js version 16.0.0 or higher is required to run the WebSocket server.');
            return;
        }
        echo shell_exec("node " . base_path('vendor/basanta/laravel-websocket/server/app.js') . " --port={$port}");
    }

}