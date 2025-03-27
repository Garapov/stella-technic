<x-guest-layout>
    @livewire('catalog.items', [
        'slug' => $slug,
        'display_filter' => true
    ])
    @livewire('main.articles')
    @livewire('main.customers')
    @livewire('main.news')
</x-guest-layout>