<?php

namespace App\Livewire\General;

use Livewire\Component;

class Certificate extends Component
{
    public $certificate;
    public $selector;

    public function mount($certificate, $selector = 'certificates')
    {
        $this->certificate = $certificate;
        $this->selector = $selector;
    }
    public function render()
    {
        return view('livewire.general.certificate', [
            'certificate' => $this->certificate,
            'selector' => $this->selector
        ]);
    }
}
