<?php

namespace App\Livewire\Dashboard\Categories\Components;

use Livewire\Attributes\Url;
use Livewire\Component;

class Products extends Component
{
    public $products;

    #[Url(as: 'products-search')]
    public ?string $search = '';

    public function mount($category_id = null)
    {
        $this->products = collect([]);
    }
    public function render()
    {
        return view('livewire.dashboard.categories.components.products');
    }
}
