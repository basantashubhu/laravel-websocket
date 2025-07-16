class mySocket
{
    connection;

    connect(host = 'localhost', port = 60001)
    {
        this.connection = new WebSocket(`ws://${host}:${port}`);
    }

    connectSecure(host = 'localhost', port = 6001)
    {
        this.connect(`wss://${host}:${port}`);
    }

    ensureConnected()
    {
        if (!this.connection || this.connection.readyState !== WebSocket.OPEN) {
            throw new Error('Socket connection not established or not open');
        }
    }

    disconnect()
    {
        this.ensureConnected();
        this.connection.close();
        this.connection = null;
    }

    on(event, cb)
    {
        this.ensureConnected();

        if(event === 'connection') {
            this.connection.onopen = cb;
        }
        else if(event === 'disconnect') {
            this.connection.onclose = cb;
        }
        else if(event === 'error') {
            this.connection.onerror = cb;
        }
        else if(event === 'message') {
            this.connection.onmessage = (e) => {
                cb(JSON.parse(e.data));
            };
        }
        else {
            throw new Error(`Unknown event: ${event}`);
        }
    }

    onEvent(event, cb)
    {
        this.ensureConnected();

        this.connection.addEventListener('message', (e) => {
            const data = JSON.parse(e.data);
            if (data.event === event) {
                cb(data.data);
            }
        });
    }
}

window.ws = new mySocket();

// ws.connect();
// ws.on('connection', console.log);
// ws.on('disconnect', console.log);
// ws.on('error', console.error);
// ws.on('message', console.log);
// ws.onEvent('report.created.FgpEtoReport', console.log);

