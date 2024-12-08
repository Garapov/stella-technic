<div class="lg:grid lg:grid-cols-2 lg:gap-8 xl:gap-16" x-data="{
    cart_quantity: 1,
    product: @js($product),
    gallery: @js($gallery),
    selectedVariation: null,
    activeTab: 'description',
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
    decrease() {
        if (this.cart_quantity < 2) return;
        this.cart_quantity--;
    },
    increase() {
        this.cart_quantity++;
    },
    validateCount() {
        if (this.cart_quantity < 1) this.cart_quantity = 1;
    },
    get productData() {
        if (this.selectedVariation) return this.selectedVariation;
            
        return this.product;
    }
}">
    <div class="grid gap-4">
        <div>
            <img class="h-auto w-full max-w-full rounded-lg"
            :src="`/storage/${productData.img.uuid}/filament-thumbnail.${productData.img.file_extension}`" alt="" />
        </div>
        <div class="grid grid-cols-5 gap-4">
            <div>
                <img class="h-auto max-w-full rounded-lg" :src="`/storage/${productData.img.uuid}/filament-thumbnail.${productData.img.file_extension}`" alt="">
            </div>
            <template x-for="image in gallery" :key="image.id">
                <div>
                    <img class="h-auto max-w-full rounded-lg" :src="`/storage/${image.uuid}/filament-thumbnail.${image.file_extension}`" alt="">
                </div>
            </template>
        </div>
    </div>

    <div class="mt-6 sm:mt-8 lg:mt-0">
        <h1 class="text-xl font-semibold text-gray-900 sm:text-2xl dark:text-white" x-text="productData.name">
            {{ $product->name }}
        </h1>
        
        <div class="mt-6 sm:gap-4 sm:items-center sm:flex sm:mt-8 mb-4">
            <a href="#" title=""
                class="flex items-center justify-center py-2.5 px-2.5 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700"
                role="button">
                <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                    width="24" height="24" fill="none" viewBox="0 0 24 24">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                        stroke-width="2"
                        d="M12.01 6.001C6.5 1 1 8 5.782 13.001L12.011 20l6.23-7C23 8 17.5 1 12.01 6.002Z" />
                </svg>
            </a>

            <button title=""
                class="text-white mt-4 sm:mt-0 bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800 flex items-center justify-center"
                role="button" @click="selectedVariation ? addVariationToCart() : addToCart()">
                <svg class="w-5 h-5 -ms-2 me-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                    width="24" height="24" fill="none" viewBox="0 0 24 24">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                        stroke-width="2"
                        d="M4 4h1.5L8 16m0 0h8m-8 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4Zm8 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4Zm.75-3H7.5M11 7H6.312M17 4v6m-3-3h6" />
                </svg>

                Add to cart
            </button>

            <div class="relative flex items-center max-w-[8rem]">
                <button type="button" id="decrement-button" data-input-counter-decrement="quantity-input" class="bg-gray-100 dark:bg-gray-700 dark:hover:bg-gray-600 dark:border-gray-600 hover:bg-gray-200 border border-gray-300 rounded-s-lg p-3 h-11 focus:ring-gray-100 dark:focus:ring-gray-700 focus:ring-2 focus:outline-none" @click="decrease">
                    <svg class="w-3 h-3 text-gray-900 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 2">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 1h16"/>
                    </svg>
                </button>
                <input type="number" id="quantity-input" data-input-counter aria-describedby="helper-text-explanation" class="bg-gray-50 border-x-0 border-gray-300 h-11 text-center text-gray-900 text-sm focus:ring-blue-500 focus:border-blue-500 block w-full py-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none" placeholder="999" x-model="cart_quantity" @change="validateCount" />
                <button type="button" id="increment-button" data-input-counter-increment="quantity-input" class="bg-gray-100 dark:bg-gray-700 dark:hover:bg-gray-600 dark:border-gray-600 hover:bg-gray-200 border border-gray-300 rounded-e-lg p-3 h-11 focus:ring-gray-100 dark:focus:ring-gray-700 focus:ring-2 focus:outline-none" @click="increase">
                    <svg class="w-3 h-3 text-gray-900 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 18">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 1v16M1 9h16"/>
                    </svg>
                </button>
            </div>
        </div>

        <ul>
            @foreach ($product->variants->groupBy('param.productParam.name') as $paramName => $variants)
                <li class="mt-4">
                    <p class="text-sm font-bold text-gray-900 dark:text-white">{{ $paramName }}</p>
                    <ul class="mt-2 flex items-center gap-4">
                        @foreach ($variants as $variant)
                            @switch ($variant->param->productParam->type)
                                @case('color')
                                    <li class="flex items-center gap-2">
                                        <div class="h-7 w-7 rounded-full" :class="{'ring-4 ring-gray-300 cursor-default': productData.id === {{ $variant->id }}, 'cursor-pointer': productData.id !== {{ $variant->id }}}" style="background-color: {{ $variant->param->value }}" x-on:click="setSelected({{ $variant }})"></div>
                                    </li>
                                    @break
                                @default
                                    <li class="flex items-center gap-2 p-3 border border- gray-200 rounded cursor-pointer" :class="{'ring-4 ring-gray-300 cursor-default': productData.id === {{ $variant->id }},  'cursor-pointer': productData.id !== {{ $variant->id }}}" x-on:click="setSelected({{ $variant }})">
                                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ $variant->param->title }}</p>
                                    </li>
                            @endswitch
                        @endforeach
                    </ul>
                </li>
            @endforeach
        </ul>

        <hr class="my-6 md:my-8 border-gray-200 dark:border-gray-800" />
        

        
    </div>

    <div class="col-span-full">
        <!-- Tabs -->
        <div class="border-b border-gray-200 dark:border-gray-700">
            <ul class="flex flex-wrap -mb-px text-sm font-medium text-center text-gray-500 dark:text-gray-400">
                <li class="mr-2">
                    <button 
                        @click="activeTab = 'description'" 
                        :class="{'text-blue-600 border-blue-600 dark:text-blue-500 dark:border-blue-500': activeTab === 'description',
                                'border-transparent hover:text-gray-600 hover:border-gray-300': activeTab !== 'description'}"
                        class="inline-flex items-center justify-center p-4 border-b-2 rounded-t-lg group">
                        Описание
                    </button>
                </li>
                <li class="mr-2">
                    <button 
                        @click="activeTab = 'specifications'" 
                        :class="{'text-blue-600 border-blue-600 dark:text-blue-500 dark:border-blue-500': activeTab === 'specifications',
                                'border-transparent hover:text-gray-600 hover:border-gray-300': activeTab !== 'specifications'}"
                        class="inline-flex items-center justify-center p-4 border-b-2 rounded-t-lg group">
                        Параметры
                    </button>
                </li>
                <!-- ... остальные табы ... -->
            </ul>
        </div>

        <div class="p-4">
            <!-- Description Tab -->
            <div x-show="activeTab === 'description'" class="text-gray-500 dark:text-gray-400">
                {!! str($product->description)->sanitizeHtml() !!}
            </div>

            <!-- Specifications Tab -->
            <div x-show="activeTab === 'specifications'" class="text-gray-500 dark:text-gray-400">
                <dl class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    @foreach ($product->variants as $variant)
                        <div class="flex justify-between">
                            <dt class="font-medium text-gray-900 dark:text-white">{{ $variant->param->productParam->name }}</dt>
                            <dd>{{ $variant->param->title }}</dd>
                        </div>
                    @endforeach
                    
                    <!-- Базовые характеристики товара, если они есть -->
                    @if($product->specifications)
                        @foreach ($product->specifications as $name => $value)
                            <div class="flex justify-between">
                                <dt class="font-medium text-gray-900 dark:text-white">{{ $name }}</dt>
                                <dd>{{ $value }}</dd>
                            </div>
                        @endforeach
                    @endif
                </dl>
            </div>
        </div>
    </div>
</div>