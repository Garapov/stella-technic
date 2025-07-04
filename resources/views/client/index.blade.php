<x-guest-layout>
    @livewire('main.slider')
    @livewire('main.features')
    @livewire('main.brands')
    {{-- @livewire('catalog.all') --}}
    {{-- @livewire('main.categories-in-block') --}}
    @livewire('main.popular')
    @livewire('main.certificates')
    @livewire('main.articles')
    @livewire('main.customers')
    @livewire('main.news')
    @livewire('general.contacts')

    <x-floating-control-panel>
        <livewire:general.panel.clear-page-cache-button />
    </x-floating-control-panel>
</x-guest-layout>