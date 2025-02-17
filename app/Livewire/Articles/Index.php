<?php

namespace App\Livewire\Articles;

use App\Models\Article;
use Livewire\Component;

class Index extends Component
{
    public function render()
    {
        return view('livewire.articles.index', [
            'items' => Article::all(),
        ]);
    }
}
