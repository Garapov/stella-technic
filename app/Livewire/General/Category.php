<?php

namespace App\Livewire\General;

use Livewire\Component;

class Category extends Component
{
    public $category;

    public function mount($category)
    {
        $this->category = $category;
    }

    public function render()
    {
        return view('livewire.general.category', [
            'category' => $this->category
        ]);
    }
}
