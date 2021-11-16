<?php

use App\Http\Controllers\SocketController;
use App\Http\Livewire\Login;
use Illuminate\Support\Facades\Route;

Route::get('/login', Login::class)->name('login');
Route::get('/socket-test', [SocketController::class,'test']);
Route::middleware('auth')->group(function () {
    Route::get('/', function () {
        return view('welcome');
    });
});
