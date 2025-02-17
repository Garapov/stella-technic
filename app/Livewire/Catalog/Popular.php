<?php

namespace App\Livewire\Catalog;

use App\Models\Product;
use Livewire\Component;

class Popular extends Component
{
    public function render()
    {
        return view('livewire.catalog.popular', [
            'items' => Product::where('is_popular', true)->pluck('id'),
            'filter' => true
        ]);
    }
}
