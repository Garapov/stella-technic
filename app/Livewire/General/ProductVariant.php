<?php

namespace App\Livewire\General;

use Livewire\Component;
use Outerweb\ImageLibrary\Models\Image;

class ProductVariant extends Component
{
    public $variant;
    public $image;

    public function mount($variant)
    {
        $this->variant = $variant;
    }

    public function render()
    {
        return view("livewire.general.product-variant");
    }
}
