<x-guest-layout>
    @php
        $products = \App\Models\Product::all();
    @endphp
    
    @livewire('catalog.items', [
        'products' => $products ? $products->pluck('id') : [],
    ])
    @livewire('main.articles')
    @livewire('main.customers')
    @livewire('main.news')
</x-guest-layout>