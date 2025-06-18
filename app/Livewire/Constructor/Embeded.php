<?php

namespace App\Livewire\Constructor;

use App\Models\ProductVariant;
use Livewire\Attributes\Url;
use Livewire\Component;

class Embeded extends Component
{
    #[Url()]
    public $variation_id;
    public $variation;
    public $added_rows = [];
    public $selectedWidth = 'slim';
    public $selectedHeight = 'low';
    public $selectedDeskType = 'Односторонняя';
    public $selectedPosition = 'on_floor';
    public $embeded = true;
    public ?int $deck_low_slim;
    public ?int $deck_high_slim;
    public ?int $deck_low_wide;
    public ?int $deck_high_wide;
    public function mount()
    {
        $this->deck_low_slim = setting("deck_low_slim", null);
        $this->deck_high_slim = setting("deck_high_slim", null);
        $this->deck_low_wide = setting("deck_low_wide", null);
        $this->deck_high_wide = setting("deck_high_wide", null);

        if ($this->variation_id) {
            $this->variation = ProductVariant::where('id', $this->variation_id)->first();

            if ($this->variation && $this->variation->is_constructable && $this->variation->constructor_type == 'deck' && $this->variation->rows) {
                $this->added_rows = $this->variation->rows;

                

                if ($this->variation->selected_width) $this->selectedWidth = $this->variation->selected_width;
                if ($this->variation->selected_height) $this->selectedHeight = $this->variation->selected_height;
                if ($this->variation->selected_desk_type) $this->selectedDeskType = $this->variation->selected_desk_type;
                if ($this->variation->selected_position) $this->selectedPosition = $this->variation->selected_position;

                // dd([$this->selectedWidth, $this->selectedHeight, $this->selectedDeskType, $this->selectedPosition]);

            }
        }
    }
    public function render()
    {
        return view("livewire.constructor.embeded", [
            'deck_low_slim' => $this->deck_low_slim,
            'deck_high_slim' => $this->deck_high_slim,
            'deck_low_wide' => $this->deck_low_wide,
            'deck_high_wide' => $this->deck_high_wide,
            'added_rows' => $this->added_rows,
            'embeded' => $this->embeded
        ]);
    }
}
