<?php

namespace App\Livewire\Main;

use App\Models\Client;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Lazy;
use Livewire\Component;
#[Lazy()]
class Customers extends Component
{
    
    public function render()
    {
        return view('livewire.main.customers');
    }

    #[Computed()]
    public function clients()
    {
        return Client::all();
    }

    public function placeholder()
    {
        return view('placeholders.main.customers');
    }
}
