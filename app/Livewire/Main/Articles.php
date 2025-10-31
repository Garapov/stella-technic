<?php

namespace App\Livewire\Main;

use App\Models\Article;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Lazy;
use Livewire\Component;

#[Lazy()]
class Articles extends Component
{

    public function render()
    {
        return view('livewire.main.articles', [
            'articles' => $this->articles
        ]);
    }
    #[Computed()]
    public function articles()
    {
        return Article::where('is_popular', true)->pluck('id');
    }

    public function placeholder()
    {
        return view('placeholders.main.articles');
    }
}
