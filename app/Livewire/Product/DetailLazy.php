<?php

namespace App\Livewire\Product;

use Livewire\Component;

class DetailLazy extends Component
{
    public $variation;
    
    public function mount($variation = null)
    {
        $this->variation = $variation;
    }
    
    public function render()
    {
        return view('livewire.product.detail-lazy');
    }
}
