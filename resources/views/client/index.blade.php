<x-guest-layout>
    @seo(['title' => 'Складское оборудование, техника для склада купить в Москве - Стелла Техник'])
    @seo(['description' => '✔ Стелла Техник: все для склада, складское оборудование, техника в Москве ✔ Продажа оборудования для склада от ведущих брендов ✔ Низкие тарифы на доставку по РФ ✔ Большие запасы и Гарантия.'])
    @seo(['image' => Storage::disk(config('filesystems.default'))->url(setting('site_logo'))])
    @seo(['url' => route('client.index')])
    <livewire:main.slider lazy="on-load" />
    <livewire:main.features lazy="on-load" />
    <livewire:main.brands lazy="on-load" />
    <livewire:main.popular lazy="on-load" />
    <livewire:main.certificates lazy="on-load" />
    <livewire:main.articles lazy="on-load" />
    <livewire:main.customers lazy="on-load" />
    <livewire:main.news lazy="on-load" />
    <livewire:general.contacts />

    <x-floating-control-panel>
        <livewire:general.panel.clear-page-cache-button />
    </x-floating-control-panel>
</x-guest-layout>