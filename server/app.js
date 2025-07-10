const http = require('http');
const crypto = require('crypto');

const server = http.createServer((req, res) => {
    if (req.method === 'POST' && req.url === '/emit') {
        let body = '';
        req.on('data', (chunk) => {
            body += chunk;
        });
        req.on('end', () => {
            try {
                const { event, data } = JSON.parse(body);
                if (!event) throw new Error('Missing event name');
                const payload = Buffer.from(JSON.stringify({ event, data }));
                for (const client of clients) {
                    sendFrame(client, payload);
                }
                res.writeHead(200, { 'Content-Type': 'application/json' });
                res.end(JSON.stringify({ status: 'ok' }));
            } catch (e) {
                res.writeHead(400, { 'Content-Type': 'application/json' });
                res.end(JSON.stringify({ error: e.message || 'Invalid JSON' }));
            }
        });
    } else {
        res.writeHead(404);
        res.end();
    }
});

const clients = new Set();

server.on('upgrade', (req, socket) => {
    // Only accept websocket upgrade requests
    if (
        req.headers['upgrade'] !== 'websocket' ||
        !req.headers['sec-websocket-key']
    ) {
        socket.end('HTTP/1.1 400 Bad Request\r\n');
        return;
    }

    // Compute accept key
    const acceptKey = crypto
        .createHash('sha1')
        .update(
            req.headers['sec-websocket-key'] +
                '258EAFA5-E914-47DA-95CA-C5AB0DC85B11'
        )
        .digest('base64');

    // Handshake response
    socket.write(
        'HTTP/1.1 101 Switching Protocols\r\n' +
            'Upgrade: websocket\r\n' +
            'Connection: Upgrade\r\n' +
            `Sec-WebSocket-Accept: ${acceptKey}\r\n` +
            '\r\n'
    );

    clients.add(socket);

    socket.on('data', (buffer) => {
        // Parse incoming frame
        if (buffer.length < 2) return;
        const secondByte = buffer[1];
        const isMasked = (secondByte & 0x80) === 0x80;
        let payloadLen = secondByte & 0x7f;
        let offset = 2;

        if (payloadLen === 126) {
            payloadLen = buffer.readUInt16BE(offset);
            offset += 2;
        } else if (payloadLen === 127) {
            payloadLen = buffer.readUInt32BE(offset + 4);
            offset += 8;
        }

        let maskingKey;
        if (isMasked) {
            maskingKey = buffer.slice(offset, offset + 4);
            offset += 4;
        }

        let payload = buffer.slice(offset, offset + payloadLen);
        if (isMasked) {
            payload = Buffer.from(
                payload.map((byte, i) => byte ^ maskingKey[i % 4])
            );
        }

        // Try to parse as JSON with event/data
        let parsed;
        try {
            parsed = JSON.parse(payload.toString());
            if (!parsed.event) throw new Error('Missing event name');
        } catch (e) {
            // Ignore invalid messages
            return;
        }

        // Broadcast to all clients
        for (const client of clients) {
            if (client !== socket) {
                sendFrame(client, Buffer.from(JSON.stringify(parsed)));
            }
        }
    });

    socket.on('close', () => {
        clients.delete(socket);
    });

    socket.on('end', () => {
        clients.delete(socket);
    });

    socket.on('error', () => {
        clients.delete(socket);
    });
});

function sendFrame(socket, data) {
    const payloadLen = data.length;
    let header;

    if (payloadLen < 126) {
        header = Buffer.alloc(2);
        header[0] = 0x81; // FIN + text frame
        header[1] = payloadLen;
    } else if (payloadLen < 65536) {
        header = Buffer.alloc(4);
        header[0] = 0x81;
        header[1] = 126;
        header.writeUInt16BE(payloadLen, 2);
    } else {
        header = Buffer.alloc(10);
        header[0] = 0x81;
        header[1] = 127;
        // Write 64-bit length, but only support up to 32-bit for simplicity
        header.writeUInt32BE(0, 2);
        header.writeUInt32BE(payloadLen, 6);
    }

    socket.write(Buffer.concat([header, data]));
}

server.listen(8080, () => {
    console.log('WebSocket server running on ws://localhost:8080');
});