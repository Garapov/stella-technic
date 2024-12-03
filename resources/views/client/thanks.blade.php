<x-guest-layout>
    @livewire('cart.thanks', ['orderId' => $orderId ?? null])
    @livewire('main.popular')
    @livewire('main.articles')
    @livewire('main.customers')
    @livewire('main.news')
</x-guest-layout>