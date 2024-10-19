<?php

namespace App\Livewire\Auth;

use Livewire\Component;

class Register extends Component
{
    public $user_type = 'natural';

    public function render()
    {
        return view('livewire.auth.register');
    }

    public function chageUserType($user_type)
    {
        $this->user_type = $user_type;
    }
}
