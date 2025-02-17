<div class="grid grid-cols-3 gap-4">
    @foreach ($posts as $post)
        @livewire('general.post', [
            'post' => $post,
        ], key($post->id))
    @endforeach
</div>
