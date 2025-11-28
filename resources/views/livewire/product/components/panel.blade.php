<div class="flex flex-col gap-4">





    <div class="flex flex-col gap-4 shadow-lg shadow-gray-900/20 bg-white p-4 rounded-xl">
        <div class="flex items-center flex-wrap justify-between gap-4">
            <div class="flex items-center gap-4">
                <h4 class="text-slate-900 text-4xl font-semibold">{{ $this->price }} </h4>
                @if (!auth()->user() && $variation->auth_price)
                    <div class="relative flex items-center gap-2 text-green-500 border border-green-500 bg-white text-md font-medium me-2 px-2.5 py-0.5 rounded-md"
                        x-data="{
                                                                                                    popover: false,
                                                                                                }"
                        @mouseover="popover = true" @mouseover.away="popover = false">
                        <span class="font-bold">{{ Number::format($variation->auth_price, 0) }} ₽</span>
                        <x-carbon-information class="w-4 h-4" />
                        <div x-show="popover" x-cloak x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 translate-y-1"
                            x-transition:enter-end="opacity-100 translate-y-0"
                            x-transition:leave="transition ease-in duration-150"
                            x-transition:leave-start="opacity-100 translate-y-0"
                            x-transition:leave-end="opacity-0 translate-y-1"
                            class="absolute bottom-full left-1/2 z-10 mb-2 -translate-x-1/2 transform rounded-md bg-gray-900 px-3 py-2 text-sm font-medium text-white shadow-sm w-[250px]">
                            <span>Эта цена доступна для авторизованных пользователей. <a class="text-blue-500"
                                    href="{{ route('login') }}" wire:navigate>Войдите</a> или <a class="text-blue-500"
                                    href="{{ route('register') }}" wire:navigate>зарегистрируйтесь</a> для применения этой
                                цены.</span>
                            <div
                                class="absolute -bottom-1 left-1/2 h-2 w-2 -translate-x-1/2 transform rotate-45 bg-gray-900">
                            </div>
                        </div>
                    </div>
                @endif

                @if ($variation->new_price)
                    <p class="text-slate-500 text-lg">
                        <strike>{{$variation->price > 0 ? Number::format($variation->price, 0) . ' ₽' : 'По запросу'}}
                        </strike>
                    </p>
                @endif
            </div>
            <div class="flex items-center gap-4">

                @if ($variation->getStatus() == 'available')
                    <div class="text-green-500 font-bold text-xl">В наличии</div>
                @elseif ($variation->getStatus() == 'unavailable')
                    <div class="text-gray-500 font-bold text-xl">Нет в наличии</div>
                    {{-- @elseif ($variation->getStatus() == 'preorder')
                    <div class="text-blue-500 font-bold text-xl">Предзаказ</div> --}}
                @endif

                <div class="relative" x-data="{ showTooltip: false }">
                    <button type="button" @mouseenter="showTooltip = true" @mouseleave="showTooltip = false"
                        @click.prevent="$store.favorites.toggleProduct({{ $variation->id }})"
                        class="rounded-lg p-2 text-slate-500 bg-white border border-slate-200 hover:bg-slate-50 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white">
                        <span class="sr-only">Добавить в избранное</span>
                        <svg class="h-7 w-7" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24"
                            :class="{ 'text-red-500 fill-red-500': $store.favorites.list[{{ $variation->id }}] }">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z" />
                        </svg>
                    </button>
                    <div x-show="showTooltip" x-cloak x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 translate-y-1"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100 translate-y-0"
                        x-transition:leave-end="opacity-0 translate-y-1"
                        class="absolute bottom-full left-1/2 z-10 mb-2 -translate-x-1/2 transform rounded-md bg-gray-900 px-3 py-2 text-sm font-medium text-white shadow-sm whitespace-nowrap">
                        <span x-show="!$store.favorites.list[{{ $variation->id }}]">Добавить в избранное</span>
                        <span x-show="$store.favorites.list[{{ $variation->id }}]">Удалить из избранного</span>
                        <div
                            class="absolute -bottom-1 left-1/2 h-2 w-2 -translate-x-1/2 transform rotate-45 bg-gray-900">
                        </div>
                    </div>
                </div>


                @if ($variation->getStatus() == 'preorder')
                    <button
                        class="rounded-lg p-3 text-blue-500 bg-white border-2 border-blue-500 md:shrink-0 shrink font-bold"
                        @click="@if (setting('open_preorder')) {{ setting('open_preorder') }} @endif $store.application.forms.preorder = true; $store.application.one_click_variation = @js($variation);">Предзаказ</button>
                @elseif($variation->getStatus() == 'unavailable')
                    <button class="rounded-lg p-3 text-white bg-gray-500 md:shrink-0 shrink font-bold"
                        @click="@if (setting('open_deadlines')) {{ setting('open_deadlines') }} @endif $store.application.forms.deadlines = true; $store.application.one_click_variation = @js($variation);">Уточнить
                        сроки</button>
                @endif
            </div>
        </div>

        @if ($variation->getStatus() == 'available')
            <div class="flex flex-wrap gap-2">
                <div
                    class="max-w-[110px] py-1 px-2 bg-white border border-slate-200 rounded-lg dark:bg-neutral-700 flex items-center">
                    <div class="flex justify-between items-center gap-x-2">
                        <div class="grow">
                            <input
                                class="w-full p-0 bg-transparent border-0 text-gray-800 font-bold focus:ring-0 [&::-webkit-inner-spin-button]:appearance-none [&::-webkit-outer-spin-button]:appearance-none "
                                style="-moz-appearance: textfield;" type="number" aria-roledescription="Quantity field"
                                x-model="cart_quantity" @change="validateQuantity">
                        </div>
                        <div class="flex justify-end items-center gap-0.5">
                            <button type="button"
                                class="size-5 inline-flex justify-center items-center gap-x-2 text-sm font-medium rounded-md border border-blue-500 bg-white text-blue-500 shadow-2xs hover:bg-gray-50 focus:outline-hidden focus:bg-gray-50 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-white dark:hover:bg-neutral-800 dark:focus:bg-neutral-800"
                                tabindex="-1" aria-label="Increase Quantity" @click="increaseQuantity">
                                <x-heroicon-o-plus class="shrink-0 size-3.5" />
                            </button>
                            <button type="button"
                                class="size-5 inline-flex justify-center items-center gap-x-2 text-sm font-medium rounded-md border border-blue-500 bg-white text-blue-500 shadow-2xs hover:bg-gray-50 focus:outline-hidden focus:bg-gray-50 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-white dark:hover:bg-neutral-800 dark:focus:bg-neutral-800"
                                tabindex="-1" aria-label="Decrease Quantity" @click="decreaseQuantity">
                                <x-heroicon-o-minus class="shrink-0 size-3.5" />
                            </button>

                        </div>
                    </div>
                </div>

                <button type="button"
                    class="text-white flex-1 bg-green-600 hover:bg-green-400 focus:ring-4 focus:outline-none focus:ring-green-300 dark:focus:ring-green-800 font-medium rounded-lg px-4 py-2 text-center flex items-center justify-center"
                    @click="addVariationToCart(); @if (setting('add_to_cart')) {{ setting('add_to_cart') }} @endif">
                    <x-fas-cart-arrow-down class="w-6 h-6 mr-2" />
                    <span>В КОРЗИНУ</span>
                </button>

                <button class="rounded-lg p-2 text-blue-500 bg-white border-2 border-blue-500 flex-1 font-bold"
                    @click="@if (setting('open_one_click')) {{ setting('open_one_click') }} @endif $store.application.forms.buy_one_click = true; $store.application.one_click_variation = @js($variation);">Купить
                    в один клик</button>



                @if ($variation->is_constructable)
                    <a href="{{ route('client.constructor', ['variation_id' => $variation->id]) }}"
                        class="w-full text-white bg-gradient-to-r from-green-400 via-green-500 to-green-600 hover:bg-gradient-to-br focus:ring-4 focus:outline-none focus:ring-green-300 dark:focus:ring-green-800 font-medium rounded-lg text-sm px-5 py-2.5 text-center">Редактировать
                        в конструкторе</a>
                @endif
            </div>
        @endif
    </div>

    <h3 class="text-md font-bold">
        Технические характиристики:
    </h3>
    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">

        @php
            $counter = 0;
        @endphp

        @foreach($this->params as $key => $paramItemGroup)
            <ul class="flex flex-col gap-4">
                @foreach ($paramItemGroup as $paramItem)
                    <li
                        class="flex items-center justify-between text-xs gap-2 @if ($counter > 3 || $paramItem->productParam->is_hidden) hidden @endif">
                        <strong class="font-medium">{{ $paramItem->productParam->name }}</strong>
                        <span class="grow border-b border-slate-300 border-dashed"></span>
                        <span class="font-bold">{{ $paramItem->title }}</span>
                    </li>
                    @php
                        $counter++;
                    @endphp
                @endforeach
            </ul>
            @php
                $counter = 0;
            @endphp
        @endforeach

        {{-- @foreach($variation->parametrs as $parametr)
        <li class="flex items-center justify-between text-sm gap-2">
            <strong class="font-medium text-slate-500">{{ $parametr->productParam->name }}</strong>
            <span class="grow border-b border-slate-300 border-dashed"></span>
            <span class="font-medium">{{ $parametr->title }}</span>
        </li>
        @endforeach --}}
    </div>
    <div class="flex items-center justify-end">
        <a href="#params" class="inline-flex items-center font-medium text-blue-600 dark:text-blue-500 hover:underline"
            @click="activeTab = 0">
            Все характиристики
            <svg class="w-4 h-4 ms-2 rtl:rotate-180" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                viewBox="0 0 14 10">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M1 5h12m0 0L9 1m4 4L9 9" />
            </svg>
        </a>
    </div>
    @if (count($this->features) > 0)
        <div class="md:grid lg:grid-cols-4 md:grid-cols-1 flex gap-4 md:overflow-x-auto overflow-x-auto">
            @foreach ($this->features as $feature)
                <div class="bg-white shadow-md rounded-lg flex p-4 h-full items-center flex flex-col gap-2 items-center">
                    <div class="min-w-10 min-h-10 w-10 h-10 text-indigo-500 flex items-center justify-center">
                        <img src="{{ Storage::disk(config("filesystems.default"))->url($feature->icon) }}" alt="">
                    </div>
                    <span
                        class="title-font text-sm font-medium text-slate-600 text-center w-[120px] md:w-auto">{{ $feature->text }}</span>
                </div>
            @endforeach
        </div>
    @endif
</div>