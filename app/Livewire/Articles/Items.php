<?php

namespace App\Livewire\Articles;

use App\Models\Article;
use Livewire\Component;

class Items extends Component
{
    public $article_ids = [];

    public function mount($articles)
    {
        $this->article_ids = $articles;
        $this->dispatch('articles_slider');
    }

    public function render()
    {
        return view('livewire.articles.items', [
            'articles' => Article::whereIn('id', $this->article_ids)->get(),
        ]);
    }
}
