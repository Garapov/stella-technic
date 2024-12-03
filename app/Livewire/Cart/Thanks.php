<?php

namespace App\Livewire\Cart;

use App\Models\Order;
use Livewire\Component;

class Thanks extends Component
{
    public $order = null;
    public $orderItems = [];

    public function mount($orderId = null)
    {
        if ($orderId) {
            try {
                $this->order = Order::findOrFail($orderId);
                $this->orderItems = $this->order->cart_items ?? [];
            } catch (\Exception $e) {
                $this->order = null;
                $this->orderItems = [];
            }
        }
    }

    public function render()
    {
        return view('livewire.cart.thanks', [
            'order' => $this->order,
            'orderItems' => $this->orderItems
        ]);
    }

    public function calculateTotal()
    {
        if (!$this->orderItems) return 0;

        return array_reduce($this->orderItems, function($total, $item) {
            $price = $item['new_price'] ?? $item['price'];
            return $total + ($price * $item['quantity']);
        }, 0);
    }
}
