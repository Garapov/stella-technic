<?php

namespace App\Livewire\Main;

use App\Models\ProductVariant;
use Livewire\Component;
use Livewire\WithPagination;

class Popular extends Component
{
    use WithPagination;

    public function render()
    {
        return view('livewire.main.popular', [
            'products' => ProductVariant::where('is_popular', true)->with([
                'paramItems',
                'parametrs',
                'product',
            ])->paginate(4, pageName: 'popular-products')
        ]);
    }
}
