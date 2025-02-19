<?php

namespace App\Livewire\Vacancies;

use Livewire\Component;
use App\Models\Vacancy;

class All extends Component
{
    public function render()
    {
        return view('livewire.vacancies.all', [
            'vacancies' => Vacancy::all(),
        ]);
    }
}
