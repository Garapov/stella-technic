<?php

namespace App\Livewire\General\Forms;

use Livewire\Component;

class Preorder extends Component
{
    public $variation = null;

    public function mount($variation = null)
    {
        $this->variation = $variation;

    }

    public function render()
    {
        return view('livewire.general.forms.preorder');
    }
}
