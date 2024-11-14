<?php

namespace App\Livewire\Main;

use App\Models\Category;
use App\Models\Feature;
use Livewire\Component;

class Features extends Component
{
    public function render()
    {
        return view('livewire.main.features', [
            'features' => Feature::all(),
            'categories' => Category::where('category_id', null)->get(),
        ]);
    }
}
