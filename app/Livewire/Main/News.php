<?php

namespace App\Livewire\Main;

use Livewire\Component;
use App\Models\Post;

class News extends Component
{
    public function render()
    {
        return view("livewire.main.news", [
            "news" => Post::where("is_popular", true)->get(),
        ]);
    }
}
