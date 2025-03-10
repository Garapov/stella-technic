<?php

namespace App\Livewire\Product;

use App\Models\Product;
use App\Models\Image;
use App\Models\ProductVariant;
use Livewire\Component;

class Detail extends Component
{
    public $product;
    public $gallery;

    public function mount($slug) {
        // dd($this->product);
        $this->product = ProductVariant::where('slug', $slug)->first();
        dd($this->product);
        $this->gallery = Image::whereIn('id', $this->product->product->gallery)->get();
        // dd($this->gallery);
    }
    public function render()
    {
        return view('livewire.product.detail', [
            'product' => $this->product,
            'gallery' => $this->gallery
        ]);
    }
}
