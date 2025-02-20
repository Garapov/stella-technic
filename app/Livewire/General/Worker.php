<?php

namespace App\Livewire\General;

use Livewire\Component;

class Worker extends Component
{
    public $worker;

    public function mount($worker)
    {
        $this->worker = $worker;
    }

    public function render()
    {
        return view('livewire.general.worker', [
            'worker' => $this->worker,
        ]);
    }
}
