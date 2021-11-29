<?php

namespace App\Http\Livewire;

use Livewire\Component;
use ElephantIO\Client;
use ElephantIO\Engine\SocketIO\Version3X;
use App\Models\ApiKey;
use Illuminate\Support\Str;
class Settings extends Component
{
    public function render()
    {
        return view('livewire.settings');
    }
    public function socketinit(){
        $client = new Client(new Version3X(env('PHP_SOCKET')));
        $client->initialize();
        $client->emit('php-init',[true]);

        // $client->keepAlive();

        $client->close();

    }
    public function apigenerate(){

        if(!ApiKey::find(1)){
            $db = new ApiKey();
            $db->user_id = '1';
            $db->api_key = Str::random(60);
            $db->save();
        }else{
            $db = ApiKey::find(1);
            $db->user_id = '1';
            $db->api_key = Str::random(60);
            $db->save();
        }
    }
    public function apirevoke(){
        ApiKey::truncate();
    }
}
