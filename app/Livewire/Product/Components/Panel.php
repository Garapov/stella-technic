<?php

namespace App\Livewire\Product\Components;

use Illuminate\Support\Number;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Lazy;
use Livewire\Component;

#[Lazy()]
class Panel extends Component
{
    public $variation;
    
    public function mount($variation = null)
    {
        $this->variation = $variation;
    }

    #[Computed()]
    public function features()
    {
        // dd($this->variation->features);
        return $this->variation ? $this->variation->product->features->sortBy('sort') : collect([]);
    }

    #[Computed()]
    public function price()
    {
        // dd($this->variation->features);
        return $this->variation->new_price ? Number::format($this->variation->new_price, locale: 'ru') : ($this->variation->price > 0 ? Number::format($this->variation->getActualPrice(), locale: 'ru') . ' ₽' : 'По запросу');
    }

    #[Computed()]
    public function params()
    {
        // dd($this->variation->features);
        return $this->variation->paramItems->merge($this->variation->parametrs)->sortBy('productParam.sort')->split(2);
    }


    public function render()
    {
        // sleep(29);  
        return view('livewire.product.components.panel');
    }

    public function placeholder()
    {
        return view('placeholders.product.panel');
    }
}
