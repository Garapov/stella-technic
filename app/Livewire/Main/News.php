<?php

namespace App\Livewire\Main;

use Livewire\Component;
use Stephenjude\FilamentBlog\Models\Post;

class News extends Component
{
    public function render()
    {
        return view('livewire.main.news', [
            'news' => Post::published()->with(['author', 'category'])->get(),
        ]);
    }
}
