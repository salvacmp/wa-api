<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use ElephantIO\Client;
use ElephantIO\Engine\SocketIO\Version3X;
use App\Helpers\Socket;
class SocketController extends Controller
{
    //
    public function test(){
        $client = new Client(new Version3X(env('PHP_SOCKET')));
        $client->initialize();
        $client->emit('sendmsg',["number"=>"085155038686","message"=>"hello"]);
        $client->close();
        // $socket = new Socket;
        // $host = explode(':',env('PHP_SOCKET'));
        // if($conn = $socket->websocket_open("localhost",17355)){
        //     $socket->websocket_write($conn,"php-init");
        //     echo "Server responed with: " . $socket->websocket_read($conn,$errstr);
        // }
    }
}
