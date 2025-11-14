<?php

namespace App\Livewire\General;

use App\Models\Former;
use App\Models\FormResult;
use App\Rules\SmartCaptchaRule;
use Livewire\Component;

class Modalformblock extends Component
{
    public $form_id;
    public $form;
    public $fields;
    public $captcha;
    public $form_name = "";
    public $confirmation = false;
    public $captcha_token = "";

    public function mount($form_id = 0, $form_name = "callback")
    {
        $this->form_id = $form_id;
        $this->form = Former::find($this->form_id);
        $this->captcha = $this->form->captcha;
        $this->form_name = $form_name;
        foreach ($this->form->fields as $field) {
            $this->fields[$field["name"]] = [
                "name" => $field["name"],
                "label" => $field["label"],
                "type" => $field["type"],
                "options" => $field["options"],
                "mask_enabled" => $field["mask_enabled"],
                "mask" => $field["mask"],
                "value" => $field["value"] ?? "",
            ];
        }
    }
    public function render()
    {
        return view("livewire.general.modalformblock");
    }

    public function messages()
    {
        return [
            "captcha_token.required" => "Вы не прошли проверку SmartCaptcha.",
        ];
    }

    public function rules()
    {
        $rules = [];
        foreach ($this->form->fields as $field) {
            if (!$field["rules"]) {
                continue;
            }
            $rules["fields." . $field["name"] . ".value"] = $field["rules"];
        }
        $rules["confirmation"] = "accepted";
        $rules["captcha_token"] = ["required", new SmartCaptchaRule()];
        // dd($rules);
        // dd($rules);
        return $rules;
    }

    public function save()
    {
        // dd($this->form);
        $validated = $this->validate();

        FormResult::create([
            "name" => $this->form->name,
            "results" => json_encode($this->fields),
            "former_id" => $this->form->id,
            "recipients" => $this->form->recipients,
        ]);

        if ($this->form->point) {
            $this->js($this->form->point); 
        }

        session()->flash("success", $this->form->thanks_text);
    }
}
