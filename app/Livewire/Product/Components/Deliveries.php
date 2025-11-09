<?php

namespace App\Livewire\Product\Components;

use App\Models\Delivery;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Lazy;
use Livewire\Component;

#[Lazy()]
class Deliveries extends Component
{
    public $variation;
    
    public function mount($variation = null)
    {
        $this->variation = $variation;
    }

    #[Computed()]
    public function deliveries()
    {
        return Delivery::where("is_active", true)->get();
    }

    public function render()
    {
        return view('livewire.product.components.deliveries');
    }
}
