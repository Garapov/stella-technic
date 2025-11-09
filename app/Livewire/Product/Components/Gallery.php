<?php

namespace App\Livewire\Product\Components;

use Livewire\Attributes\Computed;
use Livewire\Attributes\Lazy;
use Livewire\Component;

#[Lazy()]
class Gallery extends Component
{
    public $variation;
    
    public function mount($variation = null)
    {
        $this->variation = $variation;
    }

    #[Computed()]
    public function images()
    {
        return $this->variation ? $this->variation->gallery : collect([]);
    }

    #[Computed()]
    public function videos()
    {
        return $this->variation ? $this->variation->videos : collect([]);
    }

    #[Computed()]
    public function rows()
    {
        return $this->variation && $this->variation->is_constructable ? $this->variation->rows : collect([]);
    }

    public function render()
    {
        // sleep(29);
        return view('livewire.product.components.gallery');
    }

    public function placeholder()
    {
        return view('placeholders.product.gallery');
    }
}
