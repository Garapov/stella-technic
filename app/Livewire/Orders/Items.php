<?php

namespace App\Livewire\Orders;

use App\Models\Order;
use Livewire\Component;

class Items extends Component
{
    public function translateStatus($status)
    {
        $statuses = [
            'pending' => 'В обработке',
            'confirmed' => 'Подтвержден',
            'shipped' => 'Отправлен',
            'delivered' => 'Доставлен',
            'cancelled' => 'Отменен'
        ];

        return $statuses[$status] ?? $status;
    }

    public function render()
    {
        $orders = Order::where('user_id', auth()->id())
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('livewire.orders.items', [
            'orders' => $orders
        ]);
    }
}
