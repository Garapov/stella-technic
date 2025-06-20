<x-guest-layout>
    @livewire('catalog.items', [
        'path' => $path,
        'display_filter' => true
    ])
    @livewire('main.articles')
    @livewire('main.customers')
    @livewire('main.news')
</x-guest-layout>