<?php

namespace App\Livewire\Posts;

use App\Models\Post;
use Livewire\Component;

class All extends Component
{
    public function render()
    {
        return view('livewire.posts.all', [
            'posts' => Post::published()->get(),
        ]);
    }
}
