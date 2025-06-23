<x-guest-layout>
    <section class="py-8 bg-white md:py-10 dark:bg-gray-900 antialiased">
        <div class="lg:container px-4 lg:mx-auto 2xl:px-0">
            @php
               $variation = \App\Models\ProductVariant::where('slug', $product_slug)->first(); 
            @endphp
            @if ($variation)
                <div class="mb-10">{{ Breadcrumbs::render('product', $variation) }}</div>
            @endif
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
