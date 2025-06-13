<?php

namespace App\Livewire\General;

use App\Models\Former;
use App\Models\FormResult;
use App\Rules\SmartCaptchaRule;
use Livewire\Component;

class Oneclickformblock extends Component
{
    public $form_id;
    public $form;
    public $fields;
    public $captcha;
    public $form_name = "";
    public $confirmation = false;
    public $captcha_token = "";
    public $variation = null;
    public $variation_count = 1;

    public function mount($form_id = 0, $variation = null)
    {
        $this->form_id = $form_id;
        $this->form = Former::find($this->form_id);
        $this->captcha = $this->form->captcha;
        $this->variation = $variation;
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
        return view("livewire.general.oneclickformblock");
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

    public function incrementCount()
    {
        $this->variation_count++;
    }

    public function decrementCount()
    {
        $this->variation_count--;
    }

    public function validateQuantity()
    {
        if ($this->variation_count < 2) $this->variation_count = 1;
    }


    public function save()
    {
        // dd($this->form);
        $validated = $this->validate();

        $this->fields["variation"] = [
            "name" => "variation",
            "label" => "Вариация",
            "type" => "text",
            "value" => $this->variation->name,
        ];
        $this->fields["variation_count"] = [
            "name" => "variation_count",
            "label" => "Количество",
            "type" => "text",
            "value" => $this->variation_count,
        ];

        FormResult::create([
            "name" => $this->form->name,
            "results" => json_encode($this->fields),
            "former_id" => $this->form->id,
            "recipients" => $this->form->recipients,
        ]);

        session()->flash("success", $this->form->thanks_text);
    }
}
