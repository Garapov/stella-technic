<?php

namespace App\Livewire\Product;

use App\Models\Product;
use App\Models\Image;
use App\Models\ProductVariant;
use Livewire\Component;

class Detail extends Component
{
    public $product;
    public $variation;
    public $gallery;

    public function mount($slug) {
        // dd($this->product);
        $this->variation = ProductVariant::where('slug', $slug)->first();
        // dd($this->product);
        $this->gallery = Image::whereIn('id', $this->variation->product->gallery)->get();
        // dd($this->gallery);
    }
    public function render()
    {
        return view('livewire.product.detail', [
            'product' => $this->product,
            'variation' => $this->variation,
            'gallery' => $this->gallery
        ]);
    }
}
