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

        @php

            $slugs = explode('/', $path);
            $slug = end($slugs);

        @endphp

        @if ($slug)
            <livewire:general.panel.edit-resource-button link="{{ \App\Filament\Resources\ProductCategoryResource::getUrl('edit', ['record' => $slug ]) }}" title="Редактировать категорию" />
        @endif
    </x-floating-control-panel>
</x-guest-layout>