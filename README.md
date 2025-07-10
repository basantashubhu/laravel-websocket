# Laravel WebSocket

A simple and lightweight WebSocket server package for Laravel applications that enables real-time event broadcasting.

## Features

- 🚀 **Lightweight WebSocket Server**: Pure Node.js WebSocket implementation without external dependencies
- 🔄 **Real-time Event Broadcasting**: Automatic broadcasting of Laravel events to connected WebSocket clients
- 🎯 **Event-driven Architecture**: Listen to all Laravel events and broadcast specific ones
- 📡 **HTTP API**: RESTful endpoint for manual event emission
- 🔌 **Simple Integration**: Easy setup with Laravel service provider
- ⚡ **High Performance**: Minimal overhead with efficient message handling

## Installation

### 1. Install the Package

```bash
composer require basanta/laravel-websocket
```

### 2. Register the Service Provider (Optional)

**For Laravel 5.5+**: The package will be auto-discovered automatically. No manual registration required.

**For older Laravel versions**: Add the service provider to your `config/app.php`:

```php
'providers' => [
    // Other service providers...
    Basanta\LaravelWebsocket\LaravelWebsocketProvider::class,
],
```

### 3. Install Node.js Dependencies

The WebSocket server requires Guzzle HTTP client for communication:

```bash
composer require guzzlehttp/guzzle
```

### 4. Start the WebSocket Server

Navigate to the package directory and start the Node.js server:

```bash
cd vendor/basanta/laravel-websocket/server
node app.js
```

Or copy the `server/app.js` file to your project root and run:

```bash
node app.js
```

The server will start on `ws://localhost:8080`.

## Usage

### Basic Event Broadcasting

To broadcast an event via WebSocket, implement the `ShouldBroadcastWebsocket` contract in your event class:

```php
<?php

namespace App\Events;

use Basanta\LaravelWebsocket\Contract\ShouldBroadcastWebsocket;
use Illuminate\Foundation\Events\Dispatchable;

class UserMessageEvent implements ShouldBroadcastWebsocket
{
    use Dispatchable;

    public $user;
    public $message;

    public function __construct($user, $message)
    {
        $this->user = $user;
        $this->message = $message;
    }

    /**
     * Get the event name for broadcasting
     */
    public function broadcastAs()
    {
        return 'user.message';
    }

    /**
     * Get the data to broadcast
     */
    public function broadcastWith()
    {
        return [
            'user' => $this->user,
            'message' => $this->message,
            'timestamp' => now()->toISOString(),
        ];
    }
}
```

### Dispatching Events

Dispatch your events as usual, and they will automatically be broadcast to WebSocket clients:

```php
// In your controller or service
event(new UserMessageEvent($user, $message));
```

### Client-side WebSocket Connection

Connect to the WebSocket server from your frontend:

```javascript
const ws = new WebSocket('ws://localhost:8080');

ws.onopen = function() {
    console.log('Connected to WebSocket server');
};

ws.onmessage = function(event) {
    const data = JSON.parse(event.data);
    console.log('Received event:', data.event);
    console.log('Event data:', data.data);
    
    // Handle specific events
    if (data.event === 'user.message') {
        displayMessage(data.data);
    }
};

ws.onclose = function() {
    console.log('Disconnected from WebSocket server');
};

// Send events from client to server
ws.send(JSON.stringify({
    event: 'client.action',
    data: { action: 'ping' }
}));
```

### Manual Event Emission

You can also emit events directly via HTTP POST:

```bash
curl -X POST http://localhost:8080/emit \
  -H "Content-Type: application/json" \
  -d '{
    "event": "custom.event",
    "data": {
      "message": "Hello WebSocket!"
    }
  }'
```

## Configuration

### Server Configuration

The WebSocket server runs on port 8080 by default. You can modify the `server/app.js` file to change the port:

```javascript
server.listen(8080, () => {
    console.log('WebSocket server running on ws://localhost:8080');
});
```

### Laravel Configuration

The package automatically listens to all Laravel events. Events that implement `ShouldBroadcastWebsocket` will be broadcast to connected WebSocket clients.

## API Reference

### WebSocket Events

All WebSocket messages follow this structure:

```json
{
  "event": "event.name",
  "data": {
    // Event-specific data
  }
}
```

### HTTP API

#### POST /emit

Emit an event to all connected WebSocket clients.

**Request:**
```json
{
  "event": "string",
  "data": "object"
}
```

**Response:**
```json
{
  "status": "ok"
}
```

## Development

### Running the WebSocket Server

For development, you can run the server with automatic restart:

```bash
# Install nodemon globally
npm install -g nodemon

# Run with nodemon
nodemon server/app.js
```

### Testing

You can test the WebSocket connection using online WebSocket testing tools or create a simple HTML file:

```html
<!DOCTYPE html>
<html>
<head>
    <title>WebSocket Test</title>
</head>
<body>
    <div id="messages"></div>
    <script>
        const ws = new WebSocket('ws://localhost:8080');
        const messages = document.getElementById('messages');
        
        ws.onmessage = function(event) {
            const div = document.createElement('div');
            div.textContent = event.data;
            messages.appendChild(div);
        };
        
        // Send test message
        ws.onopen = function() {
            ws.send(JSON.stringify({
                event: 'test',
                data: { message: 'Hello from browser!' }
            }));
        };
    </script>
</body>
</html>
```

## Requirements

- PHP 7.4 or higher
- Laravel 8.0 or higher
- Node.js 14.0 or higher
- Composer

## License

This package is proprietary software. All rights reserved.

## Contributing

This is a private package. For feature requests or bug reports, please contact the maintainer.

## Support

For support, please contact: basanta@systha.net

## Changelog

### Version 1.0.0
- Initial release
- Basic WebSocket server implementation
- Laravel event broadcasting
- HTTP API for manual event emission
