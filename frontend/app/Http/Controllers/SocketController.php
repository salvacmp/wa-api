<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use ElephantIO\Client;
use ElephantIO\Engine\SocketIO\Version3X;

class SocketController extends Controller
{
    //
    public function test(){
        $client = new Client(new Version3X('http://localhost:8005'));
        $client->initialize();
        $client->emit('php-init', ['init' => true]);
        $client->close();
    }
}
