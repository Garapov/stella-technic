<?php

namespace App\Livewire\General;

use Livewire\Component;

class Vacancy extends Component
{
    public $vacancy;

    public function mount($vacancy)
    {
        $this->vacancy = $vacancy;
    }

    public function render()
    {
        return view('livewire.general.vacancy', [
            'vacancy' => $this->vacancy,
        ]);
    }
}
