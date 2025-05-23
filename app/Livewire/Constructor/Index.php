<?php

namespace App\Livewire\Constructor;

use App\Models\Product;
use App\Models\ProductParamItem;
use App\Models\ProductVariant;
use Livewire\Attributes\Url;
use Livewire\Component;

class Index extends Component
{
    #[Url()]
    public $variation_id;
    public $variation;
    public $addedRows = [];
    public $parent_product_id = null;
    public $selected_params = [];
    public function mount()
    {

        if ($this->variation_id) {
            $this->variation = ProductVariant::where('id', $this->variation_id)->first();
            if ($this->variation && $this->variation->is_constructable && $this->variation->constructor_type == 'deck' && $this->variation->rows) {
                $this->addedRows = $this->variation->rows;
                $this->parent_product_id = $this->variation->product->id;
            }
        }
    }
    public function render()
    {
        return view("livewire.constructor.index", [
            'added_rows' => $this->addedRows,
            'embeded' => false,
            'products' => Product::all(),
            'param_items' => ProductParamItem::all()
        ]);
    }
}
