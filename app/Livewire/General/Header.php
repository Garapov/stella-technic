<?php

namespace App\Livewire\General;

use App\Models\ProductCategory;
use App\Models\ProductVariant;
use Datlechin\FilamentMenuBuilder\Models\Menu;
use Livewire\Attributes\On;
use Livewire\Component;

class Header extends Component
{
    public function render()
    {
        // dd(ProductCategory::all());
        return view('livewire.general.header', [
            'categories' => ProductCategory::where('parent_id', -1)->get(),
            'topmenu' => Menu::location('top_menu')
        ]);
    }
    #[On('check-if-products-exists')]
    public function checkIfFavoritesExists($products)
    {
        if (!is_array($products)) return [];
        $existingProducts = ProductVariant::whereIn('id', $products)->get()->pluck('id')->toArray();
        $this->dispatch('exact-favorites', $existingProducts);
    }
}
