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
    public $constructs = [];

    public function mount()
    {
        $this->products = [];
        $this->constructs = [];
    }

    public function render()
    {
        return view("livewire.cart.index");
    }
    public function loadConstructs($constructItems = [])
    {
        $this->constructs = collect([]);
        if (empty($constructItems)) {
            return $this->constructs;
        }
        foreach($constructItems as $item ) {
            if ($item == null) continue;
            $product = ProductVariant::where("id", $item['id'])->first();
            $small_box = ProductVariant::where("id", $item['boxes']['small']['red']['id'])->first();
            $medium_box = ProductVariant::where("id", $item['boxes']['medium']['red']['id'])->first();
            $large_box = ProductVariant::where("id", $item['boxes']['large']['red']['id'])->first();

            
            if (!$product || !$small_box || !$medium_box || !$large_box) continue;

            $item['product'] = $product;
            $item['boxes']['small']['product'] = $small_box;
            $item['boxes']['medium']['product'] = $medium_box;
            $item['boxes']['large']['product'] = $large_box;

            $price = $product->price + ($small_box->price * $item['boxes']['small']['red']['count']) + ($medium_box->price * $item['boxes']['medium']['red']['count']) + ($large_box->price * $item['boxes']['large']['red']['count']);

            $item['price'] = $price;

            
            $this->constructs->put($item['id'], $item);
        }

        return $this->constructs;
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
