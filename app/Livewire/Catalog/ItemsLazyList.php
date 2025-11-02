<?php

namespace App\Livewire\Catalog;

use App\Livewire\Cart\Components\Product;
use App\Models\ProductVariant;
use App\Services\ProductSelector;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Lazy;
use Livewire\Component;
use Livewire\WithPagination;

#[Lazy()]
class ItemsLazyList extends Component
{
    use WithPagination;
    public $category = null;

    protected ProductSelector $selector;

    public function boot(ProductSelector $selector)
    {
        $this->selector = $selector;
    }

    public function mount($category = null)
    {
        $this->category = $category;
    }
    
    public function render()
    {
        // sleep(4);
        return view('livewire.catalog.items-lazy-list');
    }
    
    #[Computed()]
    public function variations()
    {
        if ($this->category) {
            return ProductVariant::whereIn('id', $this->selector->fromCategory($this->category)->pluck('id'))->paginate(40);
        }
        return collect();
    }

    public function placeholder()
    {
        // sleep(4);
        return view('placeholders.catalog.items-lazy-list');
    }
}
