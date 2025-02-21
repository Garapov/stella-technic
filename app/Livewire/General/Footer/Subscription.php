<?php

namespace App\Livewire\General\Footer;

use App\Models\Subscription as ModelsSubscription;
use Livewire\Component;

class Subscription extends Component
{
    public $email;
    public $confirmation;

    public function rules()
    {
        return [
            'email' => ['required', 'string', 'email', 'unique:subscriptions'],
            'confirmation' => ['required', 'boolean'],
        ];
    }

    public function subscribe()
    {
        $this->validate();

        ModelsSubscription::create([
            'email' => $this->email,
            'confirmation' => $this->confirmation,
        ]);

        $this->email = null;
        $this->confirmation = null;
        session()->flash('success', 'Вы успешно подписались на рассылку!');
    }

    public function render()
    {
        return view('livewire.general.footer.subscription');
    }
}
