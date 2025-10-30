<?php

namespace App\Livewire\Main;

use App\Models\Feature;
use App\Models\ProductCategory;
use App\Models\ProductVariant;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Lazy;
use Livewire\Component;
use Spatie\SchemaOrg\Schema;

#[Lazy()]
class Features extends Component
{
    #[Computed()]
    public function categories()
    {
        return ProductCategory::where('parent_id', '-1')->with([
            'categories',
            'products'
        ])->get()->sortBy('sort');
    }

    #[Computed()]
    public function features()
    {
        return Feature::where('show_on_main', true)->get()->sortBy('sort');
    }
    
    #[Computed()]
    public function featuresScheme(): string {
        $listItems = [];

        if ($this->features) {

            foreach ($this->features as $index => $feature) {
                $listItems[] = Schema::listItem()
                    ->position($index + 1)
                    ->name($feature->text);
            }
        }

        return Schema::itemList()
                ->name('Преимущества компании')
                ->itemListElement($listItems)->toScript();
    }
    
    public function render()
    {
        // sleep(20);
        return view('livewire.main.features');
    }

    public function placeholder()
    {
        return view('placeholders.main.features');
    }
}
