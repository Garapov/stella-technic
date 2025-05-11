<?php

namespace App\Livewire\Constructor;

use Livewire\Component;

class Index extends Component
{
    public $addedRows = [
        [
            "size" => "small",
            "color" => "red",
        ],
        [
            "size" => "small",
            "color" => "red",
        ],
        [
            "size" => "small",
            "color" => "green",
        ],
        [
            "size" => "small",
            "color" => "blue",
        ],
        [
            "size" => "medium",
            "color" => "yellow",
        ],
        [
            "size" => "medium",
            "color" => "gray",
        ],
        [
            "size" => "medium",
            "color" => "red",
        ],
        [
            "size" => "large",
            "color" => "green",
        ],
        [
            "size" => "large",
            "color" => "blue",
        ],
        [
            "size" => "large",
            "color" => "yellow",
        ],
    ];
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
    }
    public function render()
    {
        return view("livewire.constructor.index", [
            'deck_low_slim' => $this->deck_low_slim,
            'deck_high_slim' => $this->deck_high_slim,
            'deck_low_wide' => $this->deck_low_wide,
            'deck_high_wide' => $this->deck_high_wide,
            'added_rows' => $this->addedRows,
        ]);
    }
}
