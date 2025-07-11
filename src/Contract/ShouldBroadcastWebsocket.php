<?php

namespace Basanta\LaravelWebsocket\Contract;

interface ShouldBroadcastWebsocket
{
    /**
     * Get the event name for broadcasting.
     * If not implemented, the class name will be used.
     *
     * @return string
     */
    // public function broadcastAs(): string;

    /**
     * Get the data to broadcast.
     * If not implemented, the entire event object will be serialized.
     *
     * @return array
     */
    // public function broadcastWith(): array;

    /**
     * Get the channels to broadcast on.
     * If not implemented, broadcasts to all connected clients.
     *
     * @return array
     */
    // public function broadcastOn(): array;
}