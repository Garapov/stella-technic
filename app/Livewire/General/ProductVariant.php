<?php

namespace App\Livewire\General;

use Livewire\Component;
use Outerweb\ImageLibrary\Models\Image;

class ProductVariant extends Component
{
    public $variant;
    public $product;
    public $image;

    public function mount($variant)
    {
        $this->variant = $variant;
        $this->product = $variant->product;

        // Загружаем связь с параметром, если она не загружена
        if (!isset($variant->param) && $variant->product_param_item_id) {
            $this->variant->load("param");
        }
    }

    public function render()
    {
        return view("livewire.general.product-variant");
    }
}
