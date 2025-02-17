<x-guest-layout>
    @livewire('catalog.items', [
        'brand_slug' => $slug
    ])
    @livewire('main.articles')
    @livewire('main.customers')
    @livewire('main.news')
</x-guest-layout>