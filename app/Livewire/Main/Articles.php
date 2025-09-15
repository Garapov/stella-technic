<?php

namespace App\Livewire\Main;

use App\Models\Article;
use Livewire\Component;

class Articles extends Component
{
    public $articles;

    public function mount()
    {
        $this->articles = Article::where('is_popular', true)->pluck('id');
    }
    public function render()
    {
        return view('livewire.main.articles', [
            'articles' => $this->articles
        ]);
    }
}
