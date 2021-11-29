<?php

use App\Http\Controllers\SocketController;
use App\Http\Controllers\ApiController;

use App\Http\Livewire\Login;
use App\Http\Livewire\Dashboard;
use App\Http\Livewire\SendText;
use App\Http\Livewire\Settings;
use Illuminate\Support\Facades\Route;

Route::get('/login', Login::class)->name('login');
Route::get('/socket-test', [SocketController::class,'test']);
Route::middleware('auth')->group(function () {
    Route::get('/', Dashboard::class);
    Route::get('/send/text', SendText::class);
    Route::get('/settings', Settings::class);
});

Route::get('/{random?}/{rando?}/{rand?}/{rad?}',function(){
    return response()->json(["message"=>"WhatApp API v0.1"]);
});
