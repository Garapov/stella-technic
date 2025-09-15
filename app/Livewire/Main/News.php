<?php

namespace App\Livewire\Main;

use Livewire\Component;
use App\Models\Post;
use Illuminate\Support\Facades\Storage;
use Spatie\SchemaOrg\Schema;

class News extends Component
{
    public $news;
    public $schema;
    public function mount()
    {
        $this->news = Post::where("is_popular", true)->get();

        $itemListElements = [];
        if ($this->news) {
            foreach ($this->news as $index => $post) {
                $itemListElements[] = Schema::listItem()
                    ->position($index + 1)
                    ->item(
                        Schema::blogPosting()
                            ->headline($post->title)
                            ->url(route('client.posts.show', ['slug' => $post->slug]))
                            ->image(Storage::disk(config('filesystems.default'))->url($post->image))
                            ->datePublished($post->created_at->diffForHumans())
                            ->dateModified($post->updated_at->diffForHumans())
                            ->description($post->short_content)
                    );
            }
        }

        $this->schema = Schema::itemList()
            ->name('Посты блога')
            ->itemListElement($itemListElements)
            ->toScript();
    }
    public function render()
    {
        return view("livewire.main.news", [
            "news" => $this->news,
            "schema" => $this->schema,
        ]);
    }
}
