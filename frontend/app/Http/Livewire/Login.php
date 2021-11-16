<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
class Login extends Component
{
    public $email, $password;
    public function render()
    {
        return view('livewire.login')->layout('layouts.single');
    }
    public function login(){
        $this->validate([
            'email' => 'required|email',
            'password' => 'required|min:6'
        ]);
        $credentials = [
            'email' => $this->email,
            'password' => $this->password
        ];
        if(Auth::attempt($credentials)){
            return redirect()->intended('/');
        }
        return redirect()->back();
    }
}
