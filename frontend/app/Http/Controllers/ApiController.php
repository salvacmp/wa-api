<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use ElephantIO\Client;
use ElephantIO\Engine\SocketIO\Version3X;
class ApiController extends Controller
{
    //
    public function sendtext(Request $request){
        if($request->has('number') AND $request->has('message')){
            $client = new Client(new Version3X(env('PHP_SOCKET')));
            $client->initialize();
            $client->emit('apicheck',["ping"]);
            if(str_contains( $client->read(), "fail")){
                return response()->json(["status"=>"500","message"=>"Error, Check your socket connection!!"],500);
            }else{
                $client->emit('sendmsg',["number"=>$request->input('number'),"message"=>$request->input('message')]);
                return response()->json(["status"=>"200","message"=>"Success"],200);
            }
            $client->close();
        }else{
            return response()->json(["status"=>"400","message"=>"Bad Request!"],400);
        }
    }
}
