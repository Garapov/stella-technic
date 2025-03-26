<?php

namespace App\Livewire\Main;

use Livewire\Component;
use App\Models\Brand;

class Brands extends Component
{
    public function render()
    {
        return view('livewire.main.brands', [
            'brands' => Brand::all(),
        ]);
    }
}
