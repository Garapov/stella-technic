<div class="grid grid-cols-3 gap-4">
    @foreach ($articles as $article)
        @livewire('general.article', [
            'article' => $article,
        ], key($article->id))
    @endforeach
</div>
