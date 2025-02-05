<?php

namespace App\Livewire\Main;

use App\Models\Feature;
use App\Models\ProductCategory;
use Livewire\Component;

class Features extends Component
{
    public function render()
    {
        return view('livewire.main.features', [
            'features' => Feature::all(),
            'categories' => ProductCategory::where('parent_id', null)->get(),
        ]);
    }
}
