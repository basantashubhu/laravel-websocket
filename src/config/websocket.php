<?php

return [
    /*
    |--------------------------------------------------------------------------
    | WebSocket Server Port
    |--------------------------------------------------------------------------
    |
    | The port on which the WebSocket server will listen for connections.
    | Make sure this port is available and not blocked by firewall.
    |
    */
    'port' => env('WEBSOCKET_PORT', 6001),

    /*
    |--------------------------------------------------------------------------
    | WebSocket Server Host
    |--------------------------------------------------------------------------
    |
    | The host address for the WebSocket server. Use 'localhost' for local
    | development or '0.0.0.0' to accept connections from any address.
    |
    */
    'host' => env('WEBSOCKET_HOST', 'localhost'),

    /*
    |--------------------------------------------------------------------------
    | Connection Timeout
    |--------------------------------------------------------------------------
    |
    | The timeout in seconds for HTTP requests to the WebSocket server.
    |
    */
    'timeout' => env('WEBSOCKET_TIMEOUT', 2),

    /*
    |--------------------------------------------------------------------------
    | Enable Debug Logging
    |--------------------------------------------------------------------------
    |
    | Whether to log WebSocket broadcast failures and other debug information.
    |
    */
    'debug' => env('WEBSOCKET_DEBUG', false),
];