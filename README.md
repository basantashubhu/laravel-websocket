# Laravel WebSocket

A simple and lightweight WebSocket server package for Laravel applications that enables real-time event broadcasting.

## Features

- 🚀 **Lightweight WebSocket Server**: Pure Node.js WebSocket implementation without external dependencies
- 🔄 **Real-time Event Broadcasting**: Automatic broadcasting of Laravel events to connected WebSocket clients
- 🎯 **Event-driven Architecture**: Listen to all Laravel events and broadcast specific ones
- 📡 **HTTP API**: RESTful endpoints for manual event emission and server monitoring
- 🔌 **Simple Integration**: Easy setup with Laravel service provider
- ⚡ **High Performance**: Minimal overhead with efficient message handling
- ⚙️ **Configurable Port**: Support for custom port configuration via command-line arguments
- 📊 **Server Monitoring**: Built-in status endpoint to monitor connected clients
- 🎮 **Testing Playground**: Built-in web interface for testing WebSocket connections

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

### 3. Publish Configuration (Optional)

Publish the configuration file to customize settings:

```bash
php artisan vendor:publish --tag=websocket-config
```

This will create a `config/websocket.php` file where you can customize:
- WebSocket server port
- Host address  
- Connection timeout
- Debug logging

### 4. Install Node.js Dependencies

The WebSocket server requires Guzzle HTTP client for communication:

```bash
composer require guzzlehttp/guzzle
```

### 5. Start the WebSocket Server

#### Option 1: Using Laravel Command (Recommended)
```bash
php artisan websocket:serve
```

#### Option 2: Manual Start
Navigate to the package directory and start the Node.js server:

```bash
cd vendor/basanta/laravel-websocket/server
node app.js
```

Or copy the `server/app.js` file to your project root and run:

```bash
node app.js
```

The server will start on `ws://localhost:6001` by default.

#### Quick Test

Once the server is running, you can test it immediately by visiting the built-in playground:

```
http://your-app.test/laravel-websocket/playground
```

#### Custom Port Configuration

You can specify a custom port using command-line arguments:

```bash
# Run on port 3000
node app.js --port=3000

# Run on port 9000
node app.js --port=9000
```

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
const ws = new WebSocket('ws://localhost:6001');

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
curl -X POST http://localhost:6001/emit \
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

The WebSocket server runs on port 6001 by default. You can customize the port in several ways:

#### Method 1: Command-line Arguments
```bash
node app.js --port=3000
```

#### Method 2: Modify the Server File
You can modify the `server/app.js` file to change the default port:

```javascript
let defaultOptions = {
    port: 3000, // Change default port here
};
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

#### GET /status

Get server status and connection information.

**Response:**
```json
{
  "connectedClients": 5
}
```

**Example:**
```bash
curl http://localhost:6001/status
```

## Development

### Running the WebSocket Server

For development, you can run the server with automatic restart:

```bash
# Install nodemon globally
npm install -g nodemon

# Run with nodemon (default port 6001)
nodemon server/app.js

# Run with nodemon on custom port
nodemon server/app.js -- --port=3000
```

### Server Monitoring

You can monitor the server status and connected clients:

```bash
# Check server status
curl http://localhost:6001/status

# Response example:
# {"connectedClients": 3}
```

### Testing

#### Built-in Playground

The package includes a built-in playground for testing WebSocket connections. After starting your WebSocket server, visit:

```
http://your-app.test/laravel-websocket/playground
```

The playground provides:
- Real-time connection status
- Interactive message sending
- Live message display
- Easy testing without writing custom HTML

#### Manual Testing

You can also test the WebSocket connection using online WebSocket testing tools or create a simple HTML file:

```html
<!DOCTYPE html>
<html>
<head>
    <title>WebSocket Test</title>
</head>
<body>
    <div id="messages"></div>
    <script>
        const ws = new WebSocket('ws://localhost:6001');
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

This package is open-source software licensed under the [MIT license](LICENSE).

## Contributing

This is a private package. For feature requests or bug reports, please contact the maintainer.

## Support

For support, please contact: basanta@systha.net

## Changelog

### Version 1.1.0
- Added configurable port support via command-line arguments
- Added `/status` endpoint for server monitoring
- Improved server configuration options
- Enhanced development workflow with better port management

### Version 1.0.0
- Initial release
- Basic WebSocket server implementation
- Laravel event broadcasting
- HTTP API for manual event emission
