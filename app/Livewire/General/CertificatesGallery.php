<?php

namespace App\Livewire\General;

use Livewire\Component;

class CertificatesGallery extends Component
{
    public $certificates;
    public $title;
    public $type;

    public function mount($certificates, $title = "Наши сертификаты", $type = 'slider')
    {
        $this->certificates = $certificates;
        $this->title = $title;
        $this->type = $type;
    }

    public function render()
    {
        return view('livewire.general.certificates-gallery', [
            'certificates' => $this->certificates,
            'title' => $this->title,
            'type' => $this->type
        ]);
    }
}
