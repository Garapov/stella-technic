<x-guest-layout>
    <section class="py-8 bg-white md:py-10 dark:bg-gray-900 antialiased">
        <div class="lg:container px-4 lg:mx-auto 2xl:px-0">
            @if ($variation)
                <div class="mb-10">{{ Breadcrumbs::render('product', $variation) }}</div>
            @endif
            @livewire('product.detail', ['variation' => $variation], key($variation->slug))
        </div>
    </section>
    <x-floating-control-panel>
        <livewire:general.panel.clear-page-cache-button />

        <livewire:general.panel.edit-resource-button link="{{ \App\Filament\Resources\ProductVariantResource::getUrl('edit', ['record' => $variation->slug ]) }}" title="Редактировать товар" />

    </x-floating-control-panel>
</x-guest-layout>
