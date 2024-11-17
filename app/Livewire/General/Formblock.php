<?php

namespace App\Livewire\General;

use App\Models\Former;
use Livewire\Component;

class Formblock extends Component
{
    public $form_id;
    public $form;
    public $fields;
    public function mount($form_id = 0)
    {
        $this->form_id = $form_id;
        $this->form = Former::find($this->form_id);

        foreach($this->form->fields as $field) {
            $this->fields[$field['name']] = array(
                'name' => $field['name'],
                'label' => $field['label'],
                'type' => $field['type'],
                'options' => $field['options'],
                'value' => $field['value'] ?? ''
            );
        };
        
    }
    public function render()
    {
        return view('livewire.general.formblock');
    }

    public function rules()
    {
        $rules = array();
        foreach($this->form->fields as $field) {
            if (!$field['rules']) continue; 
            $rules['fields.'.$field['name'] . '.value'] = $field['rules'];
        };
        return $rules;
    }

    public function save()
    {
        $validated = $this->validate();

        session()->flash('success', $this->form->thanks_text);
    }
}
