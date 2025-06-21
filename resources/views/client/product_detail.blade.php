<x-guest-layout>
    <section class="py-8 bg-white md:py-10 dark:bg-gray-900 antialiased">
        <div class="lg:container px-4 lg:mx-auto 2xl:px-0">
            <div class="mb-10">@livewire('general.breadcrumbs')</div>
            @livewire('product.detail', ['slug' => $product_slug, 'path' => $path], key($product_slug . $path))
        </div>
    </section>
    <x-floating-control-panel>
        <livewire:general.panel.clear-page-cache-button />

        @if ($product_slug)
            <livewire:general.panel.edit-resource-button link="{{ \App\Filament\Resources\ProductVariantResource::getUrl('edit', ['record' => $product_slug ]) }}" title="Редактировать товар" />
        @endif
    </x-floating-control-panel>
</x-guest-layout>
