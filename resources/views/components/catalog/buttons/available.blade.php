<div class="relative w-full md:w-auto" x-data="{ showTooltip: false }" @click="showTooltip = !showTooltip"
    @click.outside="showTooltip = false">
    <button type="button"
        class="inline-flex items-center justify-center rounded-lg px-5 py-2.5 text-sm font-medium text-white focus:outline-none focus:ring-4 w-full md:w-auto bg-green-500 hover:bg-green-800 focus:ring-green-300">
        <x-carbon-shopping-cart-plus class="h-5 w-5" />
        <span class="md:sr-only ms-2">Купить</span>
    </button>
    <div x-show="showTooltip" x-cloak x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 translate-y-1"
        class="absolute bottom-full right-0 z-10 mb-2 rounded-md bg-slate-100 shadow px-3 py-2 text-sm font-medium text-white shadow-sm whitespace-nowrap">
        <div class="flex flex-col md:flex-row gap-2">
            <button
                class="inline-flex items-center rounded-md bg-blue-500 px-2 py-2 text-xs font-medium text-white border border-blue-500 inset-ring inset-ring-blue-700/10 text-center"
                @click.stop="@if (setting('open_one_click')) {{ setting('open_one_click') }} @endif $store.application.forms.buy_one_click = true; $store.application.one_click_variation = {{json_encode($variant)}}">Купить
                в один клик</button>



            <button type="button"
                class="inline-flex items-center rounded-md bg-green-500 px-2 py-2 text-xs font-medium text-white border border-green-500 inset-ring inset-ring-green-600/20 text-center"
                x-show="!$store.cart.list[{{ $variant->id }}]" @click.stop="$store.cart.addVariationToCart({
                            count: 1,
                            variationId: {{ $variant->id }},
                            name: '{{ $variant->name }}'
                        }); @if (setting('add_to_cart')) {{ setting('add_to_cart') }} @endif">
                В корзину
            </button>




            <div class="relative flex items-center" x-show="$store.cart.list[{{ $variant->id }}]">
                <button type="button"
                    class="shrink-0 bg-gray-100 dark:bg-gray-700 dark:hover:bg-gray-600 dark:border-gray-600 hover:bg-gray-200 inline-flex items-center justify-center border border-gray-300 rounded-md h-5 w-5 focus:ring-gray-100 dark:focus:ring-gray-700 focus:ring-2 focus:outline-none"
                    @click.stop="() => {
                            $store.cart.list[{{ $variant->id }}]--;
                            if ($store.cart.list[{{ $variant->id }}] < 1) {
                                $store.cart.list[{{ $variant->id }}] = 1
                            }    
                        }">
                    <svg class="w-2.5 h-2.5 text-gray-900 dark:text-white" aria-hidden="true"
                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 2">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M1 1h16" />
                    </svg>
                </button>
                <input type="number"
                    class="appearance-none [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none shrink-0 text-gray-900 dark:text-white border-0 bg-transparent text-sm font-normal focus:outline-none focus:ring-0 max-w-12 text-center"
                    @focus.stop x-model="$store.cart.list[{{ $variant->id }}]" x-on:input.debounce="(event) => {
                            if (event.target.value == '' || $store.cart.list[{{ $variant->id }}] < 1) $store.cart.list[{{ $variant->id }}] = 1;

                            console.log($store.cart.list[{{ $variant->id }}]);
                        }" />
                <button type="button"
                    class="shrink-0 bg-gray-100 dark:bg-gray-700 dark:hover:bg-gray-600 dark:border-gray-600 hover:bg-gray-200 inline-flex items-center justify-center border border-gray-300 rounded-md h-5 w-5 focus:ring-gray-100 dark:focus:ring-gray-700 focus:ring-2 focus:outline-none"
                    @click.stop="() => { $store.cart.list[{{ $variant->id }}]++ }">
                    <svg class="w-2.5 h-2.5 text-gray-900 dark:text-white" aria-hidden="true"
                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 18">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 1v16M1 9h16" />
                    </svg>
                </button>
            </div>
        </div>
        <div class="absolute -bottom-1 right-2 h-2 w-2 rotate-45 bg-slate-100 shadow"></div>
    </div>
</div>