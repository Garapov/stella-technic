<?php

namespace App\Livewire\Main;

use Livewire\Component;
use App\Models\Post;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Lazy;
use Spatie\SchemaOrg\Schema;

#[Lazy()]
class News extends Component
{
    public function render()
    {
        return view("livewire.main.news");
    }

    #[Computed()]
    public function news()
    {
        return Post::where("is_popular", true)->get();
    }

    #[Computed()]
    public function schema()
    {
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

        return Schema::itemList()
            ->name('Посты блога')
            ->itemListElement($itemListElements)
            ->toScript();
    }

    public function placeholder()
    {
        return view('placeholders.main.news');
    }
}
