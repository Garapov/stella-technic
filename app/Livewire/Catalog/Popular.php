<?php

namespace App\Livewire\Catalog;

use App\Models\Product;
use App\Models\ProductVariant;
use Livewire\Component;

class Popular extends Component
{
    public function render()
    {
        return view('livewire.catalog.popular', [
            'items' => ProductVariant::where('is_popular', true)->pluck('id'),
            'filter' => true
        ]);
    }
}
