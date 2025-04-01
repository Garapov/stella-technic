<?php

namespace App\Livewire\General;

use Livewire\Component;
use Outerweb\ImageLibrary\Models\Image;
use Livewire\Attributes\On;

class Batches extends Component
{
    public $batch;
    public function mount($batch)
    {
        $this->batch = $batch;
        // $this->image = Image::where('id', $products->image)->first(); // Assuming there's a relationship between products and images and images have a url attribute.  Replace this with your actual logic.  Also, make sure to include thelivewire-images package in your project for image rendering.  You can install it via Composer: composer require laravel/livewire-images.  Replace the image rendering logic with the Livewire-Images package's methods.  Finally, ensure that
    }
    public function render()
    {
        return view('livewire.general.batches', [
            'group' => $this->batch,
        ]);
    }
}
