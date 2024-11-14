<?php

namespace App\Livewire\Main;

use Livewire\Component;
use App\Models\Partner;

class Brands extends Component
{
    public function render()
    {
        return view('livewire.main.brands', [
            'partners' => Partner::all(),
        ]);
    }
}
