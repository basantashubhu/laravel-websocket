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

    public function register()
    {
        $this->loadRoutesFrom(__DIR__.'/routes/web.php');
        $this->loadViewsFrom(dirname(__DIR__).'/resources/views', 'laravel-websocket');
        $this->mergeConfigFrom(__DIR__.'/config/websocket.php', 'websocket');
        $this->commands([
            Commands\StartWebsocketServerCommand::class,
        ]);
    }
}