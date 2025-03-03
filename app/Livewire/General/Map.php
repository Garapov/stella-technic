<?php

namespace App\Livewire\General;

use Livewire\Component;

class Map extends Component
{
    public $points;

    public function mount($points)
    {
        $this->points = $points;
    }

    public function render()
    {
        return view('livewire.general.map', [
            'points' => $this->points,
        ]);
    }
}
