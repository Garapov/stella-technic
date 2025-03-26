<div class="rounded-lg border border-gray-200 bg-white p-0 shadow-sm dark:border-gray-700 dark:bg-gray-900 relative flex flex-col overflow-hidden">
    <div class="aspect-[1/1] w-full">
        <a href="{{ route('client.product_detail', $variant->slug) }}" wire:navigate>
            @if($variant->gallery)
                <img class="mx-auto h-full w-full object-cover"
                src="{{ Storage::disk(config('filesystems.default'))->url($variant->gallery[0]) }}" />
            @else
                <img class="mx-auto h-full w-full object-cover"
                    src="{{ asset('assets/placeholder.svg') }}" />
            @endif
        </a>
    </div>
    <div class="p-4 flex-auto shrink flex flex-col gap-2 justify-between">
        <div class="mb-4">
            <div class="mb-4 flex items-center justify-between gap-2">
                @if($variant->new_price)
                    <span class="me-2 rounded bg-blue-100 px-2.5 py-0.5 text-xs font-medium text-blue-800 dark:bg-blue-900 dark:text-blue-300 dark:text-white">
                        Скидка {{ round(100 - ($variant->new_price * 100 / $variant->price)) }}%
                    </span>
                @endif
            </div>

            <a href="{{ route('client.product_detail', $variant->slug) }}"
                class="text-md font-semibold leading-tight text-gray-900 hover:underline dark:text-white" wire:navigate>
                {{ $variant->name }} @if ($variant->product->brand) {{ $variant->product->brand->name }} @endif ({{ $variant->sku }})
            </a>
        </div>
        @if($variant->paramItems || $variant->parametrs)
            <ul class="flex flex-col gap-4 flex-grow">
                @foreach($variant->paramItems as $paramItem)
                    @if (!$paramItem->productParam->show_on_preview)
                        @continue
                    @endif
                    <li class="flex flex-center justify-between text-xs">
                        <span>{{ $paramItem->productParam->name }}</span>
                        <span>{{ $paramItem->title }}</span>
                    </li>
                @endforeach
                @foreach($variant->parametrs as $parametr)
                    @if (!$parametr->productParam->show_on_preview)
                        @continue
                    @endif
                    <li class="flex flex-center justify-between text-xs">
                        <span>{{ $parametr->productParam->name }}</span>
                        <span>{{ $parametr->title }}</span>
                    </li>
                @endforeach
            </ul>
        @endif

        <div class="mt-4 flex items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <span class="text-2xl font-extrabold leading-tight text-gray-900 dark:text-white">
                    {{ $variant->new_price ?? $variant->price }} ₽
                </span>
                @if($variant->new_price)
                    <span class="text-lg line-through font-extrabold leading-tight text-gray-600 dark:text-white">
                        {{ $variant->price }} ₽
                    </span>
                @endif
            </div>

            <div class="flex items-center gap-3">
                <div class="relative" x-data="{ showTooltip: false }">
                    <button type="button"
                        @mouseenter="showTooltip = true"
                        @mouseleave="showTooltip = false"
                        @click.prevent="$store.favorites.toggleProduct({{ $variant->id }})"
                        class="rounded-lg p-2 text-gray-500 hover:bg-gray-100 hover:text-gray-900 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white">
                        <span class="sr-only">Добавить в избранное</span>
                        <svg class="h-5 w-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                            :class="{ 'text-red-500 fill-red-500': $store.favorites.list[{{ $variant->id }}] }">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z" />
                        </svg>
                    </button>
                    <div x-show="showTooltip"
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 translate-y-1"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100 translate-y-0"
                        x-transition:leave-end="opacity-0 translate-y-1"
                        class="absolute bottom-full left-1/2 z-10 mb-2 -translate-x-1/2 transform rounded-md bg-gray-900 px-3 py-2 text-sm font-medium text-white shadow-sm whitespace-nowrap">
                        Добавить в избранное
                        <div class="absolute -bottom-1 left-1/2 h-2 w-2 -translate-x-1/2 transform rotate-45 bg-gray-900"></div>
                    </div>
                </div>

                <button type="button"
                class="inline-flex items-center rounded-lg bg-blue-500 px-5 py-2.5 text-sm font-medium text-white hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-300 dark:bg-blue-500 dark:hover:bg-blue-500 dark:focus:ring-blue-800"
                @click="$store.cart.addVariationToCart({
                    count: 1,
                    variationId: {{ $variant->id }},
                    name: '{{ $variant->name }}'
                });">
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
</div>