<?php

namespace App\Livewire\General;

use Livewire\Component;

class Category extends Component
{
    public $category;
    public $allCategoryIds;
    public $counts;
    public $minPrices;
    public $show_counts = false;
    public $show_price = false;
    public $transparent = false;

    public function mount($category, $show_counts = false, $show_price = false, $transparent = false, $allCategoryIds = [], $counts = [], $minPrices = [], $show_image = true)
    {
        $this->category = $category;
        $this->show_counts = $show_counts;
        $this->show_price = $show_price;
        $this->transparent = $transparent;
        $this->allCategoryIds = $allCategoryIds;
        $this->counts = $counts;
        $this->minPrices = $minPrices;
        $this->show_image = $show_image;
    }

    public function render()
    {
        return view('livewire.general.category', [
            'category' => $this->category,
            'show_counts' => $this->show_counts,
            'show_price' => $this->show_price,
            'show_image' => $this->show_image,
            'transparent' => $this->transparent,
            'allCategoryIds' => $this->allCategoryIds,
            'counts' => $this->counts,
            'minPrices' => $this->minPrices,
        ]);
    }
}
