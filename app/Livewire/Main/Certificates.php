<?php

namespace App\Livewire\Main;

use Livewire\Component;
use App\Models\Sertificate;

class Certificates extends Component
{
    public function render()
    {
        return view('livewire.main.certificates', [
            'certificates' => Sertificate::all(),
        ]);
    }
}
