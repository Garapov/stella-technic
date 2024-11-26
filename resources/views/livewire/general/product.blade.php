<div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-900" x-data="{
    selected: {
        name: '{{ $product->name }}',
        price: {{ $product->price }},
        new_price: {{ $product->new_price }},
        image: '{{ asset('storage/' . $image->uuid . '/filament-thumbnail.' .  $image->file_extension) }}',
        quantity: 1
    },
    setSelected: function (product) {
        $wire.getImageUrl(product.image).then(image => {

            this.selected = {
                name: product.name,
                price: product.price,
                new_price: product.new_price,
                quantity: 1,
                image: '/storage/' + image.uuid + '/filament-thumbnail.' +  image.file_extension
            };
        });
        
    }
}">
    <div class="h-56 w-full">
        <a href="{{ route('client.product_detail') }}" wire:navigate>
            <img class="mx-auto h-full dark:hidden w-full"
                :src="selected.image" alt="" />
        </a>
    </div>
    <div class="pt-6">
        <div class="mb-4 flex items-center justify-between gap-4">
            <template x-if="selected.new_price">
                <span class="me-2 rounded bg-blue-100 px-2.5 py-0.5 text-xs font-medium text-blue-800 dark:bg-blue-900 dark:text-blue-300 dark:text-white">
                Скидка <span x-text="Math.round(100 -selected.new_price * 100 / selected.price)"></span>% </span>
            </template>

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

                <button type="button" data-tooltip-target="tooltip-add-to-favorites"
                    class="rounded-lg p-2 text-gray-500 hover:bg-gray-100 hover:text-gray-900 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white">
                    <span class="sr-only"> Add to Favorites </span>
                    <svg class="h-5 w-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                            stroke-width="2" d="M12 6C6.5 1 1 8 5.8 13l6.2 7 6.2-7C23 8 17.5 1 12 6Z" />
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

        <a href="{{ route('client.product_detail') }}"
            class="text-lg font-semibold leading-tight text-gray-900 hover:underline dark:text-white" wire:navigate>{{ $product->name }}</a>
        <ul>
            @foreach ($product->variants->groupBy('param.productParam.name') as $paramName => $variants)
                <li class="mt-4">
                    <p class="text-sm font-bold text-gray-900 dark:text-white">{{ $paramName }}</p>
                    <ul class="mt-2 flex items-center gap-2">
                        @foreach ($variants as $variant)
                            @switch ($variant->param->productParam->type)
                                @case('color')
                                    <li class="flex items-center gap-2">
                                        <div class="h-4 w-4 rounded-full" style="background-color: {{ $variant->param->value }}" x-on:click="setSelected({{ $variant }})"></div>
                                    </li>
                                    @break
                                @case('size')
                                    <li class="flex items-center gap-2">
                                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400" x-on:click="setSelected({{ $variant }})">{{ $variant->param->title }}</p>
                                    </li>
                                    @break
                                @default
                                    <li class="flex items-center gap-2 p-2 border border- gray-200 rounded">
                                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400" x-on:click="setSelected({{ $variant }})">{{ $variant->param->title }}</p>
                                    </li>
                            @endswitch
                        @endforeach
                    </ul>
                </li>
            @endforeach
        </ul>

        <div class="mt-4 flex items-center justify-between gap-4">
            <template x-if="selected.new_price">
                <div class="flex items-center gap-4">
                    <p class="text-2xl font-extrabold leading-tight text-gray-900 dark:text-white"><span x-text="selected.new_price"></span> р.</p>
                    <p class="text-lg line-through font-extrabold leading-tight text-gray-600 dark:text-white"><span x-text="selected.price"></span> р.</p>
                </div>
            </template>
            <template x-if="!selected.new_price">
                <p class="text-2xl font-extrabold leading-tight text-gray-900 dark:text-white"><span x-text="selected.price"></span> р.</p>
            </template>

            <button type="button"
                class="inline-flex items-center rounded-lg bg-blue-700 px-5 py-2.5 text-sm font-medium text-white hover:bg-blue-800 focus:outline-none focus:ring-4  focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
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