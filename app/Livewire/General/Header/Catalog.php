<?php

namespace App\Livewire\General\Header;

use Livewire\Component;

class Catalog extends Component
{
    public $categories;
    public $allCategoryIds;
    public $variationCounts;
    public $minPrices;

    public function mount($categories, $allCategoryIds = [], $variationCounts = [], $minPrices = [])
    {
        $this->categories = $categories;
        $this->allCategoryIds = $allCategoryIds;
        $this->variationCounts = $variationCounts;
        $this->minPrices = $minPrices;
    }

    public function render()
    {
        return view('livewire.general.header.catalog', [
            'categories' => $this->categories,
            'variationCounts' => $this->variationCounts,
            'minPrices' => $this->minPrices,
            'allCategoryIds' => $this->allCategoryIds,
        ]);
    }
}
