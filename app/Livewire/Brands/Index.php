<?php

namespace App\Livewire\Brands;

use Livewire\Component;
use App\Models\Brand;

class Index extends Component
{
    public function render()
    {
        return view('livewire.brands.index', [
            'brands' => Brand::all(),
        ]);
    }
}
