<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>WebSocket Playground</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body { font-family: Arial, sans-serif; margin: 2em; }
        #messages { border: 1px solid #ccc; height: 300px; overflow-y: auto; padding: 1em; margin-bottom: 1em; }
        #inputArea { display: flex; gap: 0.5em; }
        #status { margin-bottom: 1em; color: green; }
    </style>
</head>
<body>
    <h2>WebSocket Playground</h2>
    <div id="status">Connecting...</div>
    <div id="messages"></div>
    <div id="inputArea">
        <input type="text" id="messageInput" placeholder="Type a message..." style="flex:1;">
        <button id="sendBtn">Send</button>
    </div>

    <script>
        let ws;
        const status = document.getElementById('status');
        const messages = document.getElementById('messages');
        const input = document.getElementById('messageInput');
        const sendBtn = document.getElementById('sendBtn');

        function appendMessage(msg, type = 'received') {
            const div = document.createElement('div');
            div.textContent = msg;
            div.style.color = type === 'sent' ? 'blue' : 'black';
            messages.appendChild(div);
            messages.scrollTop = messages.scrollHeight;
        }

        function connect() {
            // Change the URL to your websocket server if needed
            ws = new WebSocket('ws://localhost:{{ config('websocket.port') }}');
            ws.onopen = () => {
                status.textContent = 'Connected';
                status.style.color = 'green';
            };
            ws.onmessage = (e) => {
                console.log('event', e);
                
                appendMessage('Received: ' + e.data);
            };
            ws.onclose = () => {
                status.textContent = 'Disconnected';
                status.style.color = 'red';
            };
            ws.onerror = () => {
                status.textContent = 'Error';
                status.style.color = 'red';
            };
        }

        sendBtn.onclick = () => {
            const msg = input.value;
            if (ws && ws.readyState === WebSocket.OPEN && msg.trim() !== '') {
                ws.send(JSON.stringify({ event: 'message', data: msg }));
                appendMessage('Sent: ' + msg, 'sent');
                input.value = '';
            }
        };

        input.addEventListener('keyup', function(e) {
            if (e.key === 'Enter') sendBtn.click();
        });

        connect();
    </script>
</body>
</html>