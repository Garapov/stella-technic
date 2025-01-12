<?php

namespace App\Livewire\General;

use Livewire\Component;

class QuickLookModal extends Component
{
    public $product = null;
    public $show = false;

    protected $listeners = ['showQuickLook'];

    public function showQuickLook($data)
    {
        $this->product = \App\Models\Product::with(['variants.param.productParam', 'variants.img'])->find($data['id']);
        $this->show = true;
    }

    public function render()
    {
        return view('livewire.general.quick-look-modal');
    }
}
