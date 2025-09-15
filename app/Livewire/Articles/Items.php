<?php

namespace App\Livewire\Articles;

use App\Models\Article;
use Livewire\Component;
use Spatie\SchemaOrg\Schema;

class Items extends Component
{
    public $articles;
    public $schema;

    public function mount($articles)
    {
        $this->articles = Article::whereIn('id', $articles)->get();

        $itemListElements = [];
        if ($this->articles) {
            foreach ($this->articles as $index => $article) {
                $itemListElements[] = Schema::listItem()
                    ->position($index + 1)
                    ->item(
                        Schema::article()
                            ->headline($article->title)
                            ->url(route('client.articles.show', $article->slug))
                            ->datePublished($article->created_at->diffForHumans())
                            ->description($article->short_content)
                    );
            }
        }

        $this->schema = Schema::itemList()
            ->name('Статьи')
            ->itemListElement($itemListElements)
            ->toScript();

        $this->dispatch('articles_slider');
    }

    public function render()
    {
        return view('livewire.articles.items', [
            'articles' => $this->articles,
            'schema' => $this->schema
        ]);
    }
}
