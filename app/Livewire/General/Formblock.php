<?php

namespace App\Livewire\General;

use App\Models\Former;
use Livewire\Component;

class Formblock extends Component
{
    public $form_id;
    public function mount($form_id = 0)
    {
        $this->form_id = $form_id;
    }
    public function render()
    {
        return view('livewire.general.formblock', [
            'form' => Former::find($this->form_id)
        ]);
    }

    public function submit($form_data)
    {
        dd($form_data);
    }
}
