<?php

use Illuminate\Support\Facades\Route;

Route::group([
    "prefix"=> "laravel-websocket",
], function() {

    Route::get('playground', function () {
        return view('laravel-websocket::playground');
    });

});