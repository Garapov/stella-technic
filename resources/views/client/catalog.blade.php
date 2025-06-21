<x-guest-layout>
    @livewire('catalog.items', [
        'path' => $path,
        'display_filter' => true
    ])
    @livewire('main.articles')
    @livewire('main.customers')
    @livewire('main.news')

    <x-floating-control-panel>
        <livewire:general.panel.clear-page-cache-button />
    </x-floating-control-panel>
</x-guest-layout>