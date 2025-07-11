<?php

namespace Basanta\LaravelWebsocket\Listener;

use Basanta\LaravelWebsocket\Contract\ShouldBroadcastWebsocket;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class Broadcast
{
    public function handle($event)
    {
        if($event instanceof ShouldBroadcastWebsocket) {
            $this->sendHttpEmit($event);
        }
    }

    public function sendHttpEmit(ShouldBroadcastWebsocket $event)
    {
        $client = new Client();
        $payload = [
            'event' => method_exists($event, 'broadcastAs') ? $event->broadcastAs(): get_class($event),
            'data' => method_exists($event, 'broadcastWith') ? $event->broadcastWith() : $event,
        ];
        try {
            $port = config('websocket.port');
            $client->post("http://localhost:{$port}/emit", [
                'json' => $payload,
                'timeout' => 2,
            ]);
        } catch (\Exception $e) {
            // Log the error for debugging
            if (config('app.debug')) {
                Log::warning('WebSocket broadcast failed: ' . $e->getMessage(), [
                    'event' => $payload['event'],
                    'error' => $e->getMessage()
                ]);
            }
        }
    }
}