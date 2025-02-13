<?php

namespace App\Livewire\Main;

use App\Models\Product;
use Livewire\Component;
use Livewire\WithPagination;

class Popular extends Component
{
    use WithPagination;

    public function render()
    {
        return view('livewire.main.popular', [
            'products' => Product::where('is_popular', true)->paginate(4, pageName: 'popular-products')
        ]);
    }
}
