<?php

namespace App\Livewire\General;

use Livewire\Component;

class Article extends Component
{
    public $article;

    public function mount($article) {
        $this->article = $article;
    }

    public function render()
    {
        return view('livewire.general.article', [
            'article' => $this->article,
        ]);
    }
}
