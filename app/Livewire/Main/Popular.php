<?php

namespace App\Livewire\Main;

use App\Models\ProductVariant;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Lazy;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\SchemaOrg\Schema;

#[Lazy()]
class Popular extends Component
{
    use WithPagination;

    public function render()
    {
        return view('livewire.main.popular');
    }

    #[Computed()]
    public function products()
    {
        return ProductVariant::where('is_popular', true)
            ->with(['paramItems', 'parametrs', 'product'])->take(10)->get();
    }

    public function placeholder()
    {
        return view('placeholders.general.products-slider');
    }
}
