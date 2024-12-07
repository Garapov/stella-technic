<?php

namespace App\Livewire\Product;

use App\Models\Product;
use App\Models\Image;
use Livewire\Component;

class Detail extends Component
{
    public $product;
    public $gallery;

    public function mount($slug) {
        $this->product = Product::where('slug', $slug)->first();
        $this->gallery = Image::whereIn('id', $this->product->gallery)->get();
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
