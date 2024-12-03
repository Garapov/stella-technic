<?php

namespace App\Livewire\Cart;

use App\Models\Product;
use Livewire\Component;

class Index extends Component
{
    public $products = [];
    protected $listeners = ['cartUpdated' => 'handleCartUpdate'];

    public function mount()
    {
        $this->products = [];
    }

    public function render()
    {
        return view('livewire.cart.index');
    }

    public function handleCartUpdate($data = null)
    {
        if (!$data || !isset($data['products'])) {
            $this->loadProducts([]);
            return;
        }
        $this->loadProducts($data['products']);
    }

    public function loadProducts($cartItems = [])
    {
        $this->products = [];
        
        if (empty($cartItems)) {
            return $this->products;
        }

        foreach ($cartItems as $cartItem) {
            if (!$cartItem) continue;

            $product = Product::with(['variants', 'img'])->find($cartItem['id']);
            if ($product) {
                $productArray = $product->toArray();
                $productArray['quantity'] = $cartItem['count'];
                
                // Add variations data
                if (isset($cartItem['variations'])) {
                    $productArray['cart_variations'] = $cartItem['variations'];
                }
                
                $this->products[] = $productArray;
            }
        }

        return $this->products;
    }
}
