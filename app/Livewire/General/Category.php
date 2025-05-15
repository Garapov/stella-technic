<?php

namespace App\Livewire\General;

use Livewire\Component;

class Category extends Component
{
    public $category;
    public $show_counts = false;
    public $show_price = false;
    public $transparent = false;

    public function mount($category, $show_counts = false, $show_price = false, $transparent = false)
    {
        $this->category = $category;
        $this->show_counts = $show_counts;
        $this->show_price = $show_price;
        $this->transparent = $transparent;
    }

    public function render()
    {
        return view('livewire.general.category', [
            'category' => $this->category,
            'show_counts' => $this->show_counts,
            'show_price' => $this->show_price,
            'transparent' => $this->transparent,
        ]);
    }
}
