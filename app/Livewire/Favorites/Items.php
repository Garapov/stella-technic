<?php

namespace App\Livewire\Favorites;

use App\Models\Product;
use Livewire\Component;
use Livewire\Attributes\On;
use Illuminate\Database\Eloquent\Collection;

class Items extends Component
{
    public $products;
    
    public function mount()
    {
        $this->products = new Collection();
    }

    #[On('favorites-updated')]
    public function handleFavoritesUpdate($favorites)
    {
        if (is_string($favorites)) {
            $favorites = json_decode($favorites, true);
        }

        $favoriteIds = is_array($favorites) ? $favorites : [];

        if (empty($favoriteIds)) {
            $this->products = new Collection();
            return;
        }

        $this->products = Product::with(['variants', 'variants.param', 'variants.param.productParam', 'img'])
            ->whereIn('id', $favoriteIds)
            ->get();
    }

    public function render()
    {
        return view('livewire.favorites.items');
    }
}
