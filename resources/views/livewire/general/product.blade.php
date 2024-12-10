<div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-900" x-data="{
    cart_quantity: 1,
    product: @js($product),
    selectedVariation: null,
    init() {
        this.product.variants.map(variant => {
            if (variant.is_default) {
                this.selectedVariation = variant;
            }
        })
    },
    setSelected: function (variation) {
        this.selectedVariation = variation;
    },
    addToCart: function () {
        $store.cart.addToCart({
            product: this.product,
            count: this.cart_quantity
        });
    },
    addVariationToCart: function () {
        $store.cart.addVariationToCart({
            product: this.product,
            count: this.cart_quantity,
            variation: this.selectedVariation
        });
    },
    get productData() {
        if (this.selectedVariation) return this.selectedVariation;
            
        return this.product;
    }
}">
    <div class="h-56 w-full">
        <a href="{{ route('client.product_detail', $product->slug) }}" wire:navigate>
            <img class="mx-auto h-full w-full"
                :src="`/storage/${productData.img.uuid}/filament-thumbnail.${productData.img.file_extension}`" alt="" />
        </a>
    </div>
    <div class="pt-6">
        <div class="mb-4 flex items-center justify-between gap-4">
            
                <span class="me-2 rounded bg-blue-100 px-2.5 py-0.5 text-xs font-medium text-blue-800 dark:bg-blue-900 dark:text-blue-300 dark:text-white" x-show="productData.new_price">
                Скидка 
                    <span x-show="productData.new_price" x-text="Math.round(100 - (productData.new_price ?? productData.price) * 100 / productData.price)"></span>
                % </span>

            <div class="flex items-center justify-end gap-1">
                <button type="button" data-tooltip-target="tooltip-quick-look"
                    class="rounded-lg p-2 text-gray-500 hover:bg-gray-100 hover:text-gray-900 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white">
                    <span class="sr-only"> Quick look </span>
                    <svg class="h-5 w-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24"
                        height="24" fill="none" viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-width="2"
                            d="M21 12c0 1.2-4.03 6-9 6s-9-4.8-9-6c0-1.2 4.03-6 9-6s9 4.8 9 6Z" />
                        <path stroke="currentColor" stroke-width="2"
                            d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                    </svg>
                </button>
                <div id="tooltip-quick-look" role="tooltip"
                    class="tooltip invisible absolute z-10 inline-block rounded-lg bg-gray-900 px-3 py-2 text-sm font-medium text-white opacity-0 shadow-sm transition-opacity duration-300 dark:bg-gray-700"
                    data-popper-placement="top">
                    Quick look
                    <div class="tooltip-arrow" data-popper-arrow=""></div>
                </div>

                <button type="button" 
                    class="rounded-lg p-2 text-gray-500 hover:bg-gray-100 hover:text-gray-900 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white"
                    @click.prevent="$store.favorites.toggleProduct(productData.id)"
                >
                    <span class="sr-only">Добавить в избранное</span>
                    <svg class="h-5 w-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                        :class="{ 'text-red-500 fill-red-500': $store.favorites.list.includes(productData.id.toString()) }">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                            d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z" />
                    </svg>
                </button>
                <div id="tooltip-add-to-favorites" role="tooltip"
                    class="tooltip invisible absolute z-10 inline-block rounded-lg bg-gray-900 px-3 py-2 text-sm font-medium text-white opacity-0 shadow-sm transition-opacity duration-300 dark:bg-gray-700"
                    data-popper-placement="top">
                    Add to favorites
                    <div class="tooltip-arrow" data-popper-arrow=""></div>
                </div>
            </div>
        </div>

        <a href="{{ route('client.product_detail', $product->slug) }}"
            class="text-lg font-semibold leading-tight text-gray-900 hover:underline dark:text-white" wire:navigate x-text="productData.name"></a>
        <ul>
            @foreach ($product->variants->groupBy('param.productParam.name') as $paramName => $variants)
                <li class="mt-4">
                    <p class="text-sm font-bold text-gray-900 dark:text-white">{{ $paramName }}</p>
                    <ul class="mt-2 flex items-center gap-2">
                        @foreach ($variants as $variant)
                            @switch ($variant->param->productParam->type)
                                @case('color')
                                    <li class="flex items-center gap-2">
                                        <div class="h-5 w-5 rounded-full" :class="{'ring-4 ring-gray-300 cursor-default': productData.id === {{ $variant->id }}, 'cursor-pointer': productData.id !== {{ $variant->id }}}" style="background-color: {{ $variant->param->value }}" x-on:click="setSelected({{ $variant }})"></div>
                                    </li>
                                    @break
                                @default
                                    <li class="flex items-center gap-2 p-2 border border- gray-200 rounded cursor-pointer" :class="{'ring-4 ring-gray-300 cursor-default': productData.id === {{ $variant->id }},  'cursor-pointer': productData.id !== {{ $variant->id }}}" x-on:click="setSelected({{ $variant }})">
                                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400">{{ $variant->param->title }}</p>
                                    </li>
                            @endswitch
                        @endforeach
                    </ul>
                </li>
            @endforeach
        </ul>

        <div class="mt-4 flex items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                
                <p class="text-2xl font-extrabold leading-tight text-gray-900 dark:text-white"><span x-text="productData.new_price ?? productData.price"></span> р.</p>
                <p class="text-lg line-through font-extrabold leading-tight text-gray-600 dark:text-white" x-show="productData.new_price !== null"><span x-text="productData.price"></span> р.</p>
            </div>

            <button type="button"
                class="inline-flex items-center rounded-lg bg-blue-700 px-5 py-2.5 text-sm font-medium text-white hover:bg-blue-800 focus:outline-none focus:ring-4  focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800" @click="selectedVariation ? addVariationToCart() : addToCart()">
                <svg class="h-5 w-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                    width="24" height="24" fill="none" viewBox="0 0 24 24">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                        stroke-width="2"
                        d="M4 4h1.5L8 16m0 0h8m-8 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4Zm8 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4Zm.75-3H7.5M11 7H6.312M17 4v6m-3-3h6" />
                </svg>
            </button>
        </div>
    </div>
</div>