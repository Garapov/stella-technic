<x-guest-layout>
    <section class="py-4 bg-gray-50 md:py-4 antialiased">
        <div class="xl:px-[100px] px-[20px]">
            @if ($variation)
                <div class="mb-10">{{ Breadcrumbs::render('product', $variation) }}</div>
            @endif
            {{-- @livewire('product.detail', ['variation' => $variation], key($variation->slug)) --}}
            <livewire:product.detail-lazy :variation="$variation" :key="$variation->slug" />
        </div>
    </section>
    <x-floating-control-panel>
        <livewire:general.panel.clear-page-cache-button />

        <livewire:general.panel.edit-resource-button link="{{ \App\Filament\Resources\ProductVariantResource::getUrl('edit', ['record' => $variation->slug ]) }}" title="Редактировать товар" />

        <livewire:general.panel.edit-resource-button link="{{ \App\Filament\Resources\ProductResource::getUrl('edit', ['record' => $variation->product->slug ]) }}" title="Редактировать родительский товар" />

    </x-floating-control-panel>
</x-guest-layout>
