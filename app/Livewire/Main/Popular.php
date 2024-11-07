<?php

namespace App\Livewire\Main;

use App\Models\Product;
use Livewire\Component;

class Popular extends Component
{
    public function render()
    {
        return view('livewire.main.popular', [
            'products' => Product::all()
        ]);
    }
}
