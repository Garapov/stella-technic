<?php

namespace App\Livewire\Main;

use App\Models\MainSlider;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Lazy;
use Livewire\Component;

#[Lazy()]
class Slider extends Component
{
    public function render()
    {
        // sleep(10);
        return view('livewire.main.slider');
    }

    #[Computed()]
    public function slides()
    {
        return MainSlider::where('show_on_main', true)->orderBy('sort')->get();
    }

    public function placeholder()
    {
        return view('placeholders.main.slider');
    }
}


