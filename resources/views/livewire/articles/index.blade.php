<div>
    @if (count($items) > 0)
        @livewire('articles.all', ['articles' => $items])
    @endif
</div>
