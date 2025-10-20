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

        // Собираем все ID товаров и коробок (безопасно)
        $allIds = collect($constructItems)
            ->filter(fn ($item) => is_array($item) && isset($item['id']))
            ->flatMap(function ($item) {
                $ids = [$item['id']];
                if (!isset($item['boxes']) || !is_array($item['boxes'])) {
                    return $ids;
                }

                foreach ($item['boxes'] as $size => $colors) {
                    if (!is_array($colors)) continue;
                    foreach ($colors as $color => $box) {
                        if (isset($box['id'])) {
                            $ids[] = $box['id'];
                        }
                    }
                }

                return $ids;
            })
            ->unique()
            ->values();

        if ($allIds->isEmpty()) {
            return $this->constructs;
        }

        // Загружаем все варианты одним запросом
        $variants = ProductVariant::whereIn('id', $allIds)->get()->keyBy('id');

        // dd($variants);

        foreach ($constructItems as $item) {
            if (!is_array($item) || !isset($item['id'])) continue;

            $product = $variants->get($item['id']);
            if (!$product) continue;

            $item['product'] = $product;
            $totalPrice = $product->price;

            // Обрабатываем все коробки (все размеры и цвета)
            if (isset($item['boxes']) && is_array($item['boxes'])) {
                foreach ($item['boxes'] as $size => &$colors) {
                    foreach ($colors as $color => &$box) {
                        $variant = $variants->get($box['id'] ?? null);
                        if ($variant) {
                            $box['product'] = $variant;
                            $totalPrice += $variant->price * ((int)($box['count'] ?? 0));
                        }
                    }
                }
            }

            $item['price'] = $totalPrice;
            $this->constructs->put($item['id'], $item);
            // dd($this->constructs);
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
