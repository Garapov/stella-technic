<?php

namespace App\Livewire\Articles;

use Livewire\Component;

class All extends Component
{
    public $articles;

    public function mount($articles) {
        $this->articles = $articles;
    }
    public function render()
    {
        return view('livewire.articles.all', [
            'articles' => $this->articles,
        ]);
    }
}
