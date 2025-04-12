<?php

namespace App\Livewire\Constructor;

use Livewire\Component;

class Index extends Component
{
    public $addedRows = [
        [
            'size' => 'large',
            'color' => 'red'
        ],
        [
            'size' => 'large',
            'color' => 'green'
        ],
        [
            'size' => 'large',
            'color' => 'blue'
        ],
        [
            'size' => 'medium',
            'color' => 'yellow'
        ],
        [
            'size' => 'medium',
            'color' => 'gray'
        ],
        [
            'size' => 'medium',
            'color' => 'red'
        ],
        [
            'size' => 'small',
            'color' => 'green'
        ],
        [
            'size' => 'small',
            'color' => 'blue'
        ],
        [
            'size' => 'small',
            'color' => 'yellow'
        ],
        [
            'size' => 'small',
            'color' => 'gray'
        ]
    ];
    public function render()
    {
        return view('livewire.constructor.index');
    }
}
