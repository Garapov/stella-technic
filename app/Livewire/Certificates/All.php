<?php

namespace App\Livewire\Certificates;

use App\Models\Sertificate;
use Livewire\Component;

class All extends Component
{
    public function render()
    {
        return view('livewire.certificates.all', [
            'certificates' => Sertificate::all(),
        ]);
    }
}
