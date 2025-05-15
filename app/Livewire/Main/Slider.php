<?php

namespace App\Livewire\Main;

use App\Models\MainSlider;
use Livewire\Component;

class Slider extends Component
{
    public function render()
    {
        return view('livewire.main.slider', [
            'slides' => MainSlider::where('show_on_main', true)->orderBy('sort')->get()
        ]);
    }
}
