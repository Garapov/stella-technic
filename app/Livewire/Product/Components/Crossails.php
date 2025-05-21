<?php

namespace App\Livewire\Product\Components;

use Livewire\Component;

class Crossails extends Component
{
    public $title;
    public $variations;

    public function mount($title = 'Похожие товары', $variations = []) {
        $this->title = $title;
        $this->variations = $variations;
    }
    public function render()
    {
        return view('livewire.product.components.crossails');
    }
}
