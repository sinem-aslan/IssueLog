<?php

namespace App\Http\Livewire\Auth;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class Login extends Component
{
    public $email = '';
    public $password = '';

    protected $rules = [
        'email' => 'required|email',
        'password' => 'required|min:6',
    ];

    public function login()
    {
        $this->validate();

        if (Auth::attempt(['email' => $this->email, 'password' => $this->password])) {
            Session::regenerate();
            return redirect()->route('dashboard');
        } else {
            $this->addError('email', 'Email veya şifre hatalı!');
        }
    }

    public function render()
    {
        return view('livewire.auth.login')->layout('layouts.app');
    }
}
