<?php

namespace App\Livewire\Main;

use App\Models\ProductVariant;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\SchemaOrg\Schema;

class Popular extends Component
{
    use WithPagination;

    public function render()
    {
        $products = ProductVariant::where('is_popular', true)
            ->with(['paramItems', 'parametrs', 'product'])
            ->paginate(4, pageName: 'popular-products');


        return view('livewire.main.popular', [
            'products' => $products,
        ]);
    }
}
