<?php

namespace App\Livewire\General;

use App\Models\ProductVariant;
use Livewire\Component;
use Illuminate\Support\Collection;

class Recently extends Component
{
    public $variations;

    public function mount()
    {
        $this->variations = new Collection();
    }
    public function render()
    {
        return view('livewire.general.recently', [
            'variations' => $this->variations
        ]);
    }
    public function loadProducts($productIds)
    {
        $this->variations = ProductVariant::whereIn('id', $productIds)->get();
    }
}
