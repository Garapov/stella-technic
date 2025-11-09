<div x-data="{
    init() {
        $store.recently.toggleProduct({{ $variation->id }});
    }
}">
    @if ($variation->seo)

        @forelse($variation->seo as $seo_tag)
            @foreach($seo_tag['data'] as $key => $tag)

                @if ($key == 'image')
                    @seo(['image' => Storage::disk(config('filesystems.default'))->url($tag)])
                @else
                    @seo([$key => $tag])
                @endif
            @endforeach

        @empty
            @seo(['title' => $variation->h1 ?? $variation->name])
            @if ($variation->short_description)
                @seo(['description' => $variation->short_description])
            @endif
            @if ($variation->gallery)
                @seo(['image' => Storage::disk(config('filesystems.default'))->url($variation->gallery[0])])
            @endif
        @endforelse
    @else
        @seo(['title' => $variation->h1 ?? $variation->name])
        @if ($variation->short_description)
            @seo(['description' => $variation->short_description])
        @endif
        @if ($variation->gallery)
            @seo(['image' => Storage::disk(config('filesystems.default'))->url($variation->gallery[0])])
        @endif
    @endif


    <h1 class="text-lg sm:text-3xl font-semibold text-slate-700 dark:text-white mb-4">{{ $variation->h1 ?? $variation->name }} {{ $variation->sku }}</h1>

    <div class="flex md:items-center md:flex-row flex-col gap-4 mb-4">
        <div class="flex items-center gap-2">
            Артикул: <span class="text-slate-500 font-semibold">{{ $variation->sku }}</span>
        </div>
        <div class="text-slate-500 hidden md:block">|</div>
        @if ($variation->product->brand)

            <div class="flex items-center gap-2">
                Бренд: <div class="flex items-center gap-1">
                    <img src="{{ Storage::disk(config('filesystems.default'))->url($variation->product->brand->image) }}" alt="{{ $variation->product->brand->name }}" class="h-6" />
                    {{-- <span class="text-slate-500 font-semibold">{{ $variation->product->brand->name }}</span> --}}
                </div>
            </div>

        @endif
    </div>

    <div class="grid grid-cols-9 gap-8">
        <div class="flex flex-col gap-8 md:col-span-7 col-span-full">
            <div class="grid items-start grid-cols-1 lg:grid-cols-7 gap-8 max-lg:gap-12 max-sm:gap-8">
                <livewire:product.components.gallery :variation="$variation" lazy="on-load"/>
                <div class="w-full lg:sticky top-10 lg:col-span-4 col-span-full" x-data="{
                    cart_quantity: 1,
                    addVariationToCart: function () {
                        $store.cart.addVariationToCart({
                            count: this.cart_quantity,
                            variationId: {{ $variation->id }},
                            name: '{{ $variation->name }}'
                        });
                        this.cart_quantity = 1;
                    },
                    increaseQuantity() {
                        this.cart_quantity = this.cart_quantity + 1;
                    },
                    decreaseQuantity() {
                        if (this.cart_quantity <= 1) return;
                        this.cart_quantity = this.cart_quantity - 1;
                    },
                    validateQuantity() {
                        if (this.cart_quantity <= 1) this.cart_quantity = 1;
                    }
                }" wire:ignore>
                    <livewire:product.components.panel :variation="$variation" lazy="on-load"/>
                </div>
                <div class="md:col-span-3 col-span-full md:hidden block">
                    <livewire:product.components.params :variation="$variation" lazy="on-load"/>
                </div>
                
            </div>
            <livewire:product.components.tabs :variation="$variation" lazy="on-load"/>
            
        </div>
        <div class="md:col-span-2 col-span-full hidden md:block">
            <div class="md:sticky top-20">
                <livewire:product.components.params :variation="$variation" lazy="on-load"/>
                <livewire:product.components.deliveries :variation="$variation" lazy="on-load"/>
            </div>
        </div>
    </div>
    @php
        $paramItemIds = Cache::rememberForever('product_variant_param_item_ids_' . $variation->id, function () use ($variation) {
            return $variation->paramItems->merge($variation->parametrs)->filter(fn($param) => $param->productParam->is_for_crossail)->pluck('id');
        });
        $crossSellsVariants = Cache::rememberForever('product_variant_crosssell_variants_' . $variation->id, function () use ($paramItemIds, $variation) {
            return \App\Models\ProductVariant::where('product_id', $variation->product->id)->whereHas('paramItems', function ($query) use ($paramItemIds) {
                $query->whereIn('product_param_items.id', $paramItemIds);
            }) 
            ->orWhereHas('parametrs', function ($query) use ($paramItemIds) {
                $query->whereIn('product_param_items.id', $paramItemIds);
            })
            ->get()
            ->filter(function ($variant) use ($paramItemIds, $variation) {
                $allParamItems = $variant->paramItems->merge($variant->parametrs)->pluck('id')->unique();

                // Проверяем, что все элементы из $paramItemIds есть в $allParamItems
                return collect($paramItemIds)->every(function ($id) use ($allParamItems, $variation, $variant) {
                    return $allParamItems->contains($id) && $variant->product->id == $variation->product->id && $variation->id != $variant->id;
                });
            });
        });
    @endphp

    @if ($crossSellsVariants || count($variation->upSells) > 0)
        @livewire('product.components.crossails', ['title' => 'С этим товаром покупают', 'variations' => $variation->upSells->merge($crossSellsVariants)], key($variation->id))
    @endif

    @if ($variation->product->variants || count($variation->crossSells) > 0)
        @livewire('product.components.crossails', ['title' => 'Похожие товары', 'variations' => $variation->crossSells->merge($variation->product->variants->where('id', '!=', $variation->id))->take(10)], key($variation->id + rand(1,100)))
    @endif

    {{-- @livewire('general.recently', key($variation->id)) --}}
</div>
