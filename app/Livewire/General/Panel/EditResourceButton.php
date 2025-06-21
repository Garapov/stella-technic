<?php

namespace App\Livewire\General\Panel;

use Livewire\Component;

class EditResourceButton extends Component
{
    public string $link = '/';
    public string $title = 'Редактировать';
    
    public function mount(string $link = '/', string $title = 'Редактировать')
    {
        $this->link = $link;
        $this->title = $title;
    }
    
    public function render()
    {
        return view('livewire.general.panel.edit-resource-button');
    }
}
