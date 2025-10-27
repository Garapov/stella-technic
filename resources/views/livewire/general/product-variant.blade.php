<div class="rounded-lg border border-gray-200 bg-white p-0 shadow-sm dark:border-gray-700 dark:bg-gray-900 relative flex flex-col overflow-hidden relative">
    
    <div class="w-full relative" x-data="{
        imageIdToDisplay: 0,
    }">
        <div class="absolute right-2 bottom-2 top-2 z-10 flex flex-col items-end justify-between gap-2">
        
            <button type="button"
                @click.prevent="$store.favorites.toggleProduct({{ $variant->id }})"
                class="rounded-lg p-2 text-gray-500 bg-gray-100 hover:text-gray-900 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white">
                <span class="sr-only">Добавить в избранное</span>
                <svg class="h-5 w-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                    :class="{ 'text-red-500 fill-red-500': $store.favorites.list[{{ $variant->id }}] }">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z" />
                </svg>
            </button>
            
            <div class="flex flex-col items-end gap-1 pointer-events-none">
                @if ($variant->is_rebate)
                    <div class="mb-2 flex items-center justify-between gap-2">
                        <span class="me-2 rounded bg-amber-400 px-2.5 py-0.5 text-xs font-medium text-amber-900">
                            Уценка
                        </span>
                    </div>
                @endif
                @if ($variant->created_at->diffInMonths(now()) < 1)
                    <div class="mb-2 flex items-center justify-between gap-2">
                        <span class="me-2 rounded bg-teal-400 px-2.5 py-0.5 text-xs font-medium text-white">
                            Новинка
                        </span>
                    </div>
                @endif
                @if($variant->new_price)
                    <div class="mb-2 flex items-center justify-between gap-2">
                        <span class="me-2 rounded bg-red-500 px-2.5 py-0.5 text-xs font-medium text-white">
                            Скидка {{ round(100 - ($variant->new_price * 100 / $variant->getActualPrice())) }}%
                        </span>
                    </div>
                @endif
                
                @if ($variant->is_popular)
                    <div class="mb-2 flex items-center justify-between gap-2">
                        <span class="me-2 rounded bg-blue-500 px-2.5 py-0.5 text-xs font-medium text-white">
                            Популярный
                        </span>
                    </div>
                @endif
                
            </div>
        </div>
        
    @php
        // $category = $category ?? $variant->product->categories->last();
        $schema = \Spatie\SchemaOrg\Schema::product()
            ->name($variant->h1 ?? $variant->name)
            ->image(count($variant->gallery) > 0 ? Storage::disk(config('filesystems.default'))->url($variant->gallery[0]) : null)
            ->description($variant->short_description ?? $variant->description ?? '')
            ->sku($variant->sku)
            ->url(route('client.catalog', ['path' => $variant->urlChain()]))
            ->offers(
                \Spatie\SchemaOrg\Schema::offer()
                    ->priceCurrency('RUB')
                    ->price($variant->price)
                    ->availability(
                        'https://schema.org/InStock'
                    )
            );
    @endphp
        {!! $schema->toScript() !!}
        <a class="block aspect-[1/1] relative" href="{{ route('client.catalog', $variant->urlChain()) }}" wire:navigate @mouseleave="imageIdToDisplay = 0">
            @if($variant->gallery)
                @foreach($variant->gallery as $key => $image)
                    <img class="absolute top-0 left-0 mx-auto h-full w-full object-cover" src="{{ Storage::disk(config('filesystems.default'))->url($image) }}" x-show="imageIdToDisplay === {{ $key }}" :loading="imageIdToDisplay === {{ $key }} ? 'eager' : 'lazy'" />
                @endforeach
            @else
                <img class="absolute top-0 left-0 mx-auto h-full w-full object-cover"
                    src="{{ asset('assets/placeholder.svg') }}" />
            @endif
            <div class="absolute top-0 left-0 h-full w-full flex gap-0 pointer-events-none md:pointer-events-auto">
                @foreach($variant->gallery as $key => $image)
                    <div class="relative w-full" @mouseenter="imageIdToDisplay = {{ $key }}"></div>
                @endforeach
            </div>
        </a>
        @if($variant->gallery && count($variant->gallery) > 1)
            <div class="md:flex hidden items-center justify-center gap-1 absolute top-full left-0 w-full p-2">
                @foreach($variant->gallery as $key => $image)
                    <div class="w-1.5 h-1.5 rounded-full" :class="{ 'bg-blue-500': imageIdToDisplay === {{ $key }}, 'bg-gray-300': imageIdToDisplay !== {{ $key }} }"></div>
                @endforeach
            </div>
        @endif
    </div>

    <div class="flex md:hidden flex-col gap-2 px-4 pt-2 pb-0">

        <div class="flex items-center gap-4">
            <span class="md:text-2xl sm:text-xl text-lg font-semibold leading-tight  @if($variant->new_price) text-blue-500 @else text-gray-900 @endif dark:text-white">
                {{ $variant->new_price ? Number::format($variant->new_price, 0) . ' ₽' : ($variant->price > 0 ? Number::format($variant->getActualPrice(), 0) . ' ₽' : 'По запросу') }}
                
            </span>
            @if($variant->new_price)
                <span class="md:text-lg text-sm line-through leading-tight text-gray-600 dark:text-white">
                    @if ($variant->price > 0) {{ Number::format($variant->getActualPrice(), 0) }} ₽ @else По запросу @endif
                </span>
            @endif
        </div>
        @if (!auth()->user() && $variant->auth_price)
            <div class="flex items-center gap-2 text-green-800 dark:text-green-300 bg-green-100 text-md font-medium me-2 px-2.5 py-0.5 rounded-md dark:bg-green-900" x-data="{
                popover: false,
            }" @mouseover="popover = true"  @mouseover.away = "popover = false">
                <span>{{ $variant->auth_price }} ₽</span>
                <x-carbon-information class="w-4 h-4" />

                <div role="tooltip" class="absolute bottom-[calc(100%+10px)] left-0 z-10 inline-block w-full text-sm text-gray-500 transition-opacity duration-300 bg-white border border-gray-200 rounded-lg shadow-xs dark:text-gray-400 dark:border-gray-600 dark:bg-gray-800" x-show="popover">
                    <div class="px-3 py-2 bg-gray-100 border-b border-gray-200 rounded-t-lg dark:border-gray-600 dark:bg-gray-700">
                        <h3 class="font-semibold text-gray-900 dark:text-white">Цена для авторизованных пользователей</h3>
                    </div>
                    <div class="px-3 py-2">
                        <p>Эта цена доступна для авторизованных пользователей. <a class="text-blue-500" href="{{ route('login') }}" wire:navigate>Войдите</a> или <a class="text-blue-500" href="{{ route('register') }}" wire:navigate>зарегистрируйтесь</a> для применения этой цены.</p>
                    </div>
                </div>
            </div>
        @endif

    </div>
    
    <div class="md:p-4 px-4 pt-2 pb-2 flex-auto shrink flex flex-col gap-2 justify-between">
        <div class="md:mb-2">
            

            <a href="{{ route('client.catalog', $variant->urlChain()) }}"
                class="lg:text-base text-xs font-semibold leading-tight text-gray-900 hover:underline dark:text-white" wire:navigate>
                {{ $variant->name ?? $variant->h1 }} {{ $variant->sku }}
            </a>
        </div>
        @if($variant->paramItems || $variant->parametrs)
            @php
                $show_params = false;
                foreach($variant->paramItems->merge($variant->parametrs)->sortBy('productParam.sort') as $paramItem):
                    if (!$paramItem->productParam->show_on_preview || $show_params) continue;
                    $show_params = true;
                endforeach
            @endphp
            @if ($show_params)
                <div class="flex-grow hidden md:block">
                    <ul class="flex flex-col gap-1 p-2 bg-slate-50 rounded-lg shadow-sm">
                        @foreach($variant->paramItems->merge($variant->parametrs)->sortBy('productParam.sort') as $paramItem)
                            @if (!$paramItem->productParam->show_on_preview)
                                @continue
                            @endif
                            <li class="flex items-center gap-1 justify-between dark:text-white">
                                <span class="md:text-sm text-xs">{{ $paramItem->productParam->name }}</span>
                                <span class="grow border-b border-dashed"></span>
                                <span class="md:text-sm text-xs font-medium">{{ $paramItem->title }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif
        @endif

        <div class="flex">
            @if ($variant->count > 0 && !$variant->is_pre_order)
                @if ($variant->price > 0)
                    <div class="text-green-600 text-sm font-medium me-2">В наличии</div>
                @else
                    <div class="text-blue-600 text-sm font-medium me-2">Под заказ</div>
                @endif
            @endif
            @if ($variant->count < 1)
                <div class="text-gray-800 text-sm font-medium me-2">Нет в наличии</div>
            @endif
            @if ($variant->count > 0 && $variant->is_pre_order)
                <div class="text-yellow-800 text-sm font-medium me-2">Предзаказ</div>
            @endif
        </div>

        <div class="flex items-end justify-between gap-4 relative">
            <div class="md:flex hidden flex-col gap-2">

                <div class="flex items-center gap-4">
                    <span class="text-2xl font-semibold leading-tight @if($variant->new_price) text-blue-500 @else text-gray-900 @endif dark:text-white">
                        {{ $variant->new_price ? Number::format($variant->new_price, 0) . ' ₽' : ($variant->price > 0 ? Number::format($variant->getActualPrice(), 0) . ' ₽' : 'По запросу') }} 
                        
                    </span>
                    @if($variant->new_price)
                        <span class="text-lg line-through leading-tight text-gray-600 dark:text-white">
                           @if ($variant->price > 0) {{ Number::format($variant->getActualPrice(), 0) }} ₽ @else По запросу @endif
                        </span>
                    @endif
                </div>
                @if (!auth()->user() && $variant->auth_price)
                    <div class="flex items-center gap-2 text-green-800 dark:text-green-300 bg-green-100 text-md font-medium me-2 px-2.5 py-0.5 rounded-md dark:bg-green-900" x-data="{
                        popover: false,
                    }" @mouseover="popover = true"  @mouseover.away = "popover = false">
                        <span>{{ $variant->auth_price }} ₽</span>
                        <x-carbon-information class="w-4 h-4" />

                        <div role="tooltip" class="absolute bottom-[calc(100%+10px)] left-0 z-10 inline-block w-full text-sm text-gray-500 transition-opacity duration-300 bg-white border border-gray-200 rounded-lg shadow-xs dark:text-gray-400 dark:border-gray-600 dark:bg-gray-800" x-show="popover">
                            <div class="px-3 py-2 bg-gray-100 border-b border-gray-200 rounded-t-lg dark:border-gray-600 dark:bg-gray-700">
                                <h3 class="font-semibold text-gray-900 dark:text-white">Цена для авторизованных пользователей</h3>
                            </div>
                            <div class="px-3 py-2">
                                <p>Эта цена доступна для авторизованных пользователей. <a class="text-blue-500" href="{{ route('login') }}" wire:navigate>Войдите</a> или <a class="text-blue-500" href="{{ route('register') }}" wire:navigate>зарегистрируйтесь</a> для применения этой цены.</p>
                            </div>
                        </div>
                    </div>
                @endif

            </div>

            <div class="flex items-center gap-3 w-full md:w-auto">
                
                @if ($variant->count > 0)
                    





                    <div class="relative w-full md:w-auto" x-data="{ showTooltip: false }" @click="showTooltip = !showTooltip"
                            @click.outside="showTooltip = false">
                        <button type="button"
                            
                            class="inline-flex items-center justify-center rounded-lg bg-green-500 px-5 py-2.5 text-sm font-medium text-white hover:bg-green-800 focus:outline-none focus:ring-4 focus:ring-green-300 w-full md:w-auto">
                            <x-carbon-shopping-cart-plus class="h-5 w-5" />
                            <span class="md:sr-only ms-2">Купить</span>
                        </button>
                        <div x-show="showTooltip"
                            x-cloak
                            x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 translate-y-1"
                            x-transition:enter-end="opacity-100 translate-y-0"
                            x-transition:leave="transition ease-in duration-150"
                            x-transition:leave-start="opacity-100 translate-y-0"
                            x-transition:leave-end="opacity-0 translate-y-1"
                            class="absolute bottom-full right-0 z-10 mb-2 rounded-md bg-slate-100 shadow px-3 py-2 text-sm font-medium text-white shadow-sm whitespace-nowrap">
                            <div class="flex flex-col md:flex-row gap-2">
                                <button class="inline-flex items-center rounded-md bg-blue-500 px-2 py-2 text-xs font-medium text-white border border-blue-500 inset-ring inset-ring-blue-700/10 text-center" @click.stop="$store.application.forms.buy_one_click = true, $store.application.one_click_variation = {{json_encode($variant)}}">Купить в один клик</button>



                                <button type="button"
                                    class="inline-flex items-center rounded-md bg-green-500 px-2 py-2 text-xs font-medium text-white border border-green-500 inset-ring inset-ring-green-600/20 text-center" x-show="!$store.cart.list[{{ $variant->id }}]"
                                    @click.stop="$store.cart.addVariationToCart({
                                        count: 1,
                                        variationId: {{ $variant->id }},
                                        name: '{{ $variant->name }}'
                                    });">
                                        В корзину
                                    </button>

                                    


                                    <div class="relative flex items-center" x-show="$store.cart.list[{{ $variant->id }}]">
                                        <button type="button" class="shrink-0 bg-gray-100 dark:bg-gray-700 dark:hover:bg-gray-600 dark:border-gray-600 hover:bg-gray-200 inline-flex items-center justify-center border border-gray-300 rounded-md h-5 w-5 focus:ring-gray-100 dark:focus:ring-gray-700 focus:ring-2 focus:outline-none" @click.stop="() => {
                                            $store.cart.list[{{ $variant->id }}]--;
                                            if ($store.cart.list[{{ $variant->id }}] < 1) {
                                                $store.cart.list[{{ $variant->id }}] = 1
                                            }    
                                        }">
                                            <svg class="w-2.5 h-2.5 text-gray-900 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 2">
                                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 1h16"/>
                                            </svg>
                                        </button>
                                        <input type="number" class="appearance-none [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none shrink-0 text-gray-900 dark:text-white border-0 bg-transparent text-sm font-normal focus:outline-none focus:ring-0 max-w-12 text-center" @focus.stop x-model="$store.cart.list[{{ $variant->id }}]" x-on:input.debounce="(event) => {
                                            if (event.target.value == '' || $store.cart.list[{{ $variant->id }}] < 1) $store.cart.list[{{ $variant->id }}] = 1;

                                            console.log($store.cart.list[{{ $variant->id }}]);
                                        }" />
                                        <button type="button" class="shrink-0 bg-gray-100 dark:bg-gray-700 dark:hover:bg-gray-600 dark:border-gray-600 hover:bg-gray-200 inline-flex items-center justify-center border border-gray-300 rounded-md h-5 w-5 focus:ring-gray-100 dark:focus:ring-gray-700 focus:ring-2 focus:outline-none" @click.stop="() => { $store.cart.list[{{ $variant->id }}]++ }">
                                            <svg class="w-2.5 h-2.5 text-gray-900 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 18">
                                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 1v16M1 9h16"/>
                                            </svg>
                                        </button>
                                    </div>
                            </div>
                            <div class="absolute -bottom-1 right-2 h-2 w-2 rotate-45 bg-slate-100 shadow"></div>
                        </div>
                    </div>
                @endif
                @if ($variant->count < 1)
                    <button type="button"
                    class="pointer-events-none inline-flex items-center rounded-lg border border-slate-400 px-5 py-2.5 text-sm font-medium text-slate-400">
                        <x-carbon-shopping-cart-error  class="h-5 w-5" />
                    </button>
                @endif
            </div>


        </div>
    </div>
</div>
