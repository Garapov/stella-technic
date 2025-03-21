<?php

namespace App\Livewire\Favorites;

use App\Models\Product;
use App\Models\ProductVariant;
use Livewire\Component;
use Livewire\Attributes\On;
use Illuminate\Database\Eloquent\Collection;

class Items extends Component
{
    public $products;
    public $isLoading = true;

    public function mount()
    {
        $this->products = new Collection();
    }

    public function loadFavorites($favorites)
    {
        $favoriteIds = is_array($favorites) ? $favorites : [];

        foreach ($favorites as $key => $favorite) {
            if (!$favorite) {
                continue;
            }
            $favoriteIds[] = $key;
        }

        if (empty($favoriteIds)) {
            $this->products = new Collection();
            return;
        }

        $this->products = ProductVariant::whereIn("id", $favoriteIds)->get();

        $this->isLoading = false;
    }

    #[On("favorites-updated")]
    public function handleFavoritesUpdate($favorites)
    {
        $this->loadFavorites($favorites);
    }

    public function render()
    {
        return view("livewire.favorites.items");
    }
}
