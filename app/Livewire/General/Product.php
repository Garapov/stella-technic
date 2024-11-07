<?php

namespace App\Livewire\General;

use Livewire\Component;

class Product extends Component
{
    public $product;
    public function mount($product)
    {
        $this->product = $product;
    }
    public function render()
    {
        return view('livewire.general.product');
    }
}
