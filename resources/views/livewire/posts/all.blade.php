<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 xl:grid-cols-5 gap-4">
    @foreach ($posts as $post)
        @livewire('general.post', [
            'post' => $post,
        ], key($post->id))
    @endforeach
</div>
