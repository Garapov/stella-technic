<?php

namespace App\Livewire\Main;

use App\Models\Client;
use Livewire\Component;

class Customers extends Component
{
    public function render()
    {
        return view('livewire.main.customers', [
            'clients' => Client::all()
        ]);
    }
}
