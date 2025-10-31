<?php

namespace App\Livewire\General;

use Livewire\Attributes\Lazy;
use Livewire\Component;

#[Lazy()]
class Contacts extends Component
{
    public function render()
    {
        return view('livewire.general.contacts');
    }

    public function placeholder()
    {
        return view('placeholders.general.contacts');
    }
}
