<?php

namespace Basanta\LaravelWebsocket\Listener;

use Basanta\LaravelWebsocket\Contract\ShouldBroadcastWebsocket;
use GuzzleHttp\Client;

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
            $client->post('http://localhost:8080/emit', [
                'json' => $payload,
                'timeout' => 2,
            ]);
        } catch (\Exception $e) {
            // Optionally log or handle the error
        }
    }
}