<x-guest-layout>
    @seo(['title' => 'Складское оборудование, техника для склада купить в Москве - Стелла Техник'])
    @seo(['description' => '✔ Стелла Техник: все для склада, складское оборудование, техника в Москве ✔ Продажа оборудования для склада от ведущих брендов ✔ Низкие тарифы на доставку по РФ ✔ Большие запасы и Гарантия.'])
    @seo(['image' => Storage::disk(config('filesystems.default'))->url(setting('site_logo'))])
    @seo(['url' => route('client.index')])
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