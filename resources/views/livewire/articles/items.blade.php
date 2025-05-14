<div>
    
    <div class="glide__track" data-glide-el="track">
        <div class="glide__slides">
            @foreach ($articles as $article)
                @livewire('general.article', [
                    'article' => $article,
                ], key($article->id))
            @endforeach
        </div>
    </div>
</div>