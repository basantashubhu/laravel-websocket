<?php

namespace Basanta\LaravelWebsocket;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class LaravelWebsocketProvider extends ServiceProvider
{
    public function boot()
    {
        Event::listen('*', function($eventName, $data) {
            app(Listener\Broadcast::class)->handle($data[0]);
        });
    }
}