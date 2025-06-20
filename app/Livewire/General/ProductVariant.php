<?php

namespace App\Livewire\General;

use Livewire\Component;
use Outerweb\ImageLibrary\Models\Image;

class ProductVariant extends Component
{
    public $variant;
    public $category;
    public $image;

    public function mount($variant, $category = null)
    {
        $this->variant = $variant;
        $this->category = $category;
    }

    public function render()
    {
        return view("livewire.general.product-variant");
    }
}
