<?php

namespace App\Livewire\General;

use Livewire\Component;

class Certificate extends Component
{
    public $certificate;

    public function mount($certificate)
    {
        $this->certificate = $certificate;
    }
    public function render()
    {
        return view('livewire.general.certificate', [
            'certificate' => $this->certificate,
        ]);
    }
}
