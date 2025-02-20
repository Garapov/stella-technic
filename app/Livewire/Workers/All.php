<?php

namespace App\Livewire\Workers;

use Livewire\Component;
use App\Models\Worker;

class All extends Component
{
    public function render()
    {
        return view('livewire.workers.all', [
            'workers' => Worker::where('is_active', true)->get(),
        ]);
    }
}
