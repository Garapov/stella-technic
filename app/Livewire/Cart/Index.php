<?php

namespace App\Livewire\Cart;

use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class Index extends Component
{
    public $products = [];

    public function mount()
    {
        $this->products = [];
    }

    public function render()
    {
        return view("livewire.cart.index");
    }

    public function loadProducts($cartItems = [])
    {
        $this->products = new Collection();

        if (empty($cartItems)) {
            return $this->products;
        }

        $productIds = [];

        foreach ($cartItems as $key => $item) {
            if ($item == null) {
                continue;
            }
            $productIds[] = $key;
        }

        $this->products = ProductVariant::whereIn("id", $productIds)->get();

        return $this->products;
    }
}
