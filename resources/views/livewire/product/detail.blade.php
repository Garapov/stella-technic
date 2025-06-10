<div x-data="{
    product: @js($variation->product),
    variation: @js($variation),
    cart_quantity: 1,
    gallerySlider: null,
    thumbnailSlider: null,
    init() {
        if (document.querySelector('.gallery-slider')) {
            setTimeout(() => {
                    this.gallerySlider = new window.splide('.gallery-slider', {
                      type       : 'fade',
                      heightRatio: 1,
                      pagination : true,
                      arrows     : false,
                      cover      : true,
                      rewind          : true,
                    });

                    this.thumbnailSlider = new window.splide('.gallery-thumbnails', {
                      rewind          : true,
                      isNavigation    : true,
                      gap             : 10,
                      perPage : 7,
                      heightRatio: 0.1,
                      focus           : 'center',
                      pagination      : false,
                      cover           : true,
                      direction       : 'ttb',
                      paginationDirection: 'ltr',
                      dragMinThreshold: {
                        mouse: 4,
                        touch: 10,
                      },
                    });

                    this.gallerySlider.sync( this.thumbnailSlider );
                    this.gallerySlider.mount();
                    this.thumbnailSlider.mount();
            }, 0)
        }
    },
    addVariationToCart: function () {
        $store.cart.addVariationToCart({
            count: this.cart_quantity,
            variationId: this.variation.id,
            name: this.variation.name
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
    },
    downloadFile(key) {
        $wire.downloadFile(key).then(result => {
            console.log(result);
        })
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
            @seo(['title' => $variation->name])
            @if ($variation->short_description)
                @seo(['description' => $variation->short_description])
            @endif
            @if ($variation->gallery)
                @seo(['image' => Storage::disk(config('filesystems.default'))->url($variation->gallery[0])])
            @endif
        @endforelse
    @else
        @seo(['title' => $variation->name])
        @if ($variation->short_description)
            @seo(['description' => $variation->short_description])
        @endif
        @if ($variation->gallery)
            @seo(['image' => Storage::disk(config('filesystems.default'))->url($variation->gallery[0])])
        @endif
    @endif

    <div class="grid items-start grid-cols-1 lg:grid-cols-5 gap-8 max-lg:gap-12 max-sm:gap-8">
        <div class="w-full lg:sticky top-10 col-span-2">
            <div class="flex flex-col-reverse gap-4" x-ignore>
                @if ($variation->gallery)
                    <div class="grid grid-cols-8 gap-2">
                        <section class="splide gallery-thumbnails" aria-label="Splide Basic HTML Example" ref="gallerySlider">
                            <div class="h-full">
                                <div class="splide__track vertical-carousel-height-fix">
                                    <ul class="splide__list">
                                        @foreach ($variation->gallery as $image)
                                            <li class="splide__slide"> <img src="{{ Storage::disk(config('filesystems.default'))->url($image) }}" alt="Product1" class="w-full  aspect-[1/1] object-cover"  /></li>
                                        @endforeach
                                        @if ($variation->is_constructable && $variation->rows)
                                            <li class="splide__slide">
                                                <img src="{{ asset('assets/3dicon.png') }}" alt="3dicon" class="w-full  aspect-[1/1] object-cover"  />
                                            </li>
                                        @endif
                                    </ul>
                                </div>
                            </div>
                        </section>
                        <section class="splide gallery-slider col-span-7" aria-label="Splide Basic HTML Example" ref="gallerySlider">
                            <div class="splide__track">
                                <ul class="splide__list">
                                    @foreach ($variation->gallery as $image)
                                        <li class="splide__slide"> <img src="{{ Storage::disk(config('filesystems.default'))->url($image) }}" alt="Product1" class="w-full  aspect-[1/1] object-cover"  /></li>
                                    @endforeach
                                    @if ($variation->is_constructable && $variation->rows)
                                        <li class="splide__slide">
                                            <iframe name="constructor" src="{{ route('client.constructor_embeded', ['variation_id' => $variation->id]) }}" frameborder="no" border="0" scrolling="no" width="100%" height="100%"></iframe>
                                        </li>
                                    @endif
                                </ul>
                            </div>
                        </section>

                    </div>
                @else
                    <img src="{{ asset('assets/placeholder.svg') }}" alt="Product1" class="w-full  aspect-[1/1] object-cover"  />
                @endif
            </div>
        </div>

        <div class="w-full lg:sticky top-10 col-span-3">
            <h1 class="text-lg sm:text-3xl font-semibold text-slate-700 dark:text-white">{{ $variation->name }} @if ($variation->product->brand){{$variation->product->brand->name}}@endif ({{$variation->sku}})</h1>

            <hr class="my-6 border-slate-300" />

            <div class="grid grid-cols-2 items-start gap-4">

                <div class="flex flex-col gap-2">
                    <div class="parametrs">
                        @foreach($groupedParams as $paramGroup)
                            <div class="mb-6">
                                @php
                                    $activeParamName = collect(array_filter($paramGroup['values'], fn ($paramValue) => $paramValue['is_current']));
                                @endphp
                                <h3 class="text-lg sm:text-sm font-semibold text-slate-900 dark:text-white @if (count($activeParamName) < 1) hidden @endif">{{ $paramGroup['name'] }} @if ($activeParamName->first())({{$activeParamName->first()['title']}})@endif</h3>
                                <div class="flex flex-wrap gap-4 mt-2 @if (count($activeParamName) < 1) hidden @endif">
                                    @foreach($paramGroup['values'] as $value)
                                        @if($paramGroup['name'] === 'Цвет')
                                            <a href="{{ route('client.product_detail', $variation->product->variants->where('id', $value['variant_id'])->first()->slug) }}" wire:navigate
                                                @class([
                                                    'relative flex items-center gap-2 border rounded-full',
                                                    'border-blue-600' => $value['is_current'],
                                                    'border-slate-300 hover:border-blue-600' => !$value['is_current'] && $value['is_available'],
                                                    'border-slate-200 opacity-30' => !$value['is_available'],
                                                ])

                                                @if(!$value['is_available']) disabled @endif>
                                                @php
                                                    $colors = explode('|', $value['value']);
                                                @endphp
                                                <div class="relative w-8 h-8 rounded-full border @if($value['is_current']) border-blue-500 @else border-gray-300 @endif overflow-hidden">
                                                    @if(count($colors) > 1)
                                                        <div class="absolute inset-0">
                                                            <div class="h-full w-1/2 float-left" style="background-color: {{ trim($colors[0]) }}"></div>
                                                            <div class="h-full w-1/2 float-right" style="background-color: {{ trim($colors[1]) }}"></div>
                                                        </div>
                                                    @else
                                                        <div class="absolute inset-0" style="background-color: {{ trim($colors[0]) }}"></div>
                                                    @endif
                                                </div>
                                            </a>
                                        @else
                                            <a href="{{ route('client.product_detail', $variation->product->variants->where('id', $value['variant_id'])->first()->slug) }}"
                                                @class([
                                                    'px-2 py-2 border text-sm flex items-center justify-center shrink-0 text-xs rounded-xl dark:text-white',
                                                    'bg-blue-600 text-white' => $value['is_current'],
                                                    'border-slate-300 hover:border-blue-600' => !$value['is_current'] && $value['is_available'],
                                                    'border-slate-200 opacity-30' => !$value['is_available'],
                                                    'hidden' => !$value['is_current'] && $value['is_fixed'],
                                                ]) wire:navigate >
                                                {{ $value['title'] }}
                                            </a>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>



                </div>

                <div class="flex flex-col gap-4">
                    <div class="flex flex-col gap-4 border border-blue-500 p-4 rounded-xl">
                        <div class="flex items-center flex-wrap justify-between gap-4">
                            <h4 class="text-slate-900 text-4xl font-semibold">{{ $variation->new_price ? Number::format($variation->new_price, 0) : Number::format($variation->getActualPrice(), 0) }} ₽</h4>
                            @if (!auth()->user() && $variation->auth_price)
                                <div class="relative flex items-center gap-2 text-green-800 dark:text-green-300 bg-green-100 text-md font-medium me-2 px-2.5 py-0.5 rounded-md dark:bg-green-900" x-data="{
                                    popover: false,
                                }" @mouseover="popover = true"  @mouseover.away = "popover = false">
                                    <span>{{ $variation->auth_price }} ₽</span>
                                    <x-carbon-information class="w-4 h-4" />
                                    <div x-show="popover"
                                        x-cloak
                                        x-transition:enter="transition ease-out duration-200"
                                        x-transition:enter-start="opacity-0 translate-y-1"
                                        x-transition:enter-end="opacity-100 translate-y-0"
                                        x-transition:leave="transition ease-in duration-150"
                                        x-transition:leave-start="opacity-100 translate-y-0"
                                        x-transition:leave-end="opacity-0 translate-y-1"
                                        class="absolute bottom-full left-1/2 z-10 mb-2 -translate-x-1/2 transform rounded-md bg-gray-900 px-3 py-2 text-sm font-medium text-white shadow-sm w-[250px]">
                                        <span>Эта цена доступна для авторизованных пользователей. <a class="text-blue-500" href="{{ route('login') }}" wire:navigate>Войдите</a> или <a class="text-blue-500" href="{{ route('register') }}" wire:navigate>зарегистрируйтесь</a> для применения этой цены.</span>
                                        <div class="absolute -bottom-1 left-1/2 h-2 w-2 -translate-x-1/2 transform rotate-45 bg-gray-900"></div>
                                    </div>
                                </div>
                            @endif

                            @if ($variation->new_price)
                                <p class="text-slate-500 text-lg"><strike>{{Number::format($variation->price, 0)}} ₽</strike></p>
                            @endif
                        </div>

                        <div class="flex flex-wrap items-center gap-4">

                            <div class="max-w-40 py-2 px-3 bg-gray-100 rounded-lg dark:bg-neutral-700">
                                <div class="flex justify-between items-center gap-x-5">
                                    <div class="grow">
                                        <input class="w-full p-0 bg-transparent border-0 text-gray-800 focus:ring-0 [&::-webkit-inner-spin-button]:appearance-none [&::-webkit-outer-spin-button]:appearance-none dark:text-white" style="-moz-appearance: textfield;" type="number" aria-roledescription="Quantity field" x-model="cart_quantity" @change="validateQuantity">
                                    </div>
                                    <div class="flex justify-end items-center gap-x-1.5">
                                        <button type="button" class="size-8 inline-flex justify-center items-center gap-x-2 text-sm font-medium rounded-md border border-gray-200 bg-white text-gray-800 shadow-2xs hover:bg-gray-50 focus:outline-hidden focus:bg-gray-50 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-white dark:hover:bg-neutral-800 dark:focus:bg-neutral-800" tabindex="-1" aria-label="Decrease Quantity" @click="decreaseQuantity">
                                            <x-heroicon-o-minus class="shrink-0 size-3.5" />
                                        </button>
                                        <button type="button" class="size-8 inline-flex justify-center items-center gap-x-2 text-sm font-medium rounded-md border border-gray-200 bg-white text-gray-800 shadow-2xs hover:bg-gray-50 focus:outline-hidden focus:bg-gray-50 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-white dark:hover:bg-neutral-800 dark:focus:bg-neutral-800" tabindex="-1" aria-label="Increase Quantity" @click="increaseQuantity">
                                            <x-heroicon-o-plus class="shrink-0 size-3.5" />
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="relative" x-data="{ showTooltip: false }">
                                <button type="button"
                                    @mouseenter="showTooltip = true"
                                    @mouseleave="showTooltip = false"
                                    @click.prevent="$store.favorites.toggleProduct({{ $variation->id }})"
                                    class="rounded-lg p-2 text-gray-500 bg-gray-100 text-gray-900 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white">
                                    <span class="sr-only">Добавить в избранное</span>
                                    <svg class="h-7 w-7" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        :class="{ 'text-red-500 fill-red-500': $store.favorites.list[{{ $variation->id }}] }">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z" />
                                    </svg>
                                </button>
                                <div x-show="showTooltip"
                                    x-cloak
                                    x-transition:enter="transition ease-out duration-200"
                                    x-transition:enter-start="opacity-0 translate-y-1"
                                    x-transition:enter-end="opacity-100 translate-y-0"
                                    x-transition:leave="transition ease-in duration-150"
                                    x-transition:leave-start="opacity-100 translate-y-0"
                                    x-transition:leave-end="opacity-0 translate-y-1"
                                    class="absolute bottom-full left-1/2 z-10 mb-2 -translate-x-1/2 transform rounded-md bg-gray-900 px-3 py-2 text-sm font-medium text-white shadow-sm whitespace-nowrap">
                                    <span x-show="!$store.favorites.list[{{ $variation->id }}]">Добавить в избранное</span>
                                    <span x-show="$store.favorites.list[{{ $variation->id }}]">Удалить из избранного</span>
                                    <div class="absolute -bottom-1 left-1/2 h-2 w-2 -translate-x-1/2 transform rotate-45 bg-gray-900"></div>
                                </div>
                            </div>
                            <div class="grow flex flex-col gap-2">
                                <button class="font-medium text-blue-600 dark:text-blue-500 underline">Купить в один клик</button>
                            </div>
                            <button type="button" class="w-full text-white bg-gradient-to-r from-green-400 via-green-500 to-green-600 hover:bg-gradient-to-br focus:ring-4 focus:outline-none focus:ring-green-300 dark:focus:ring-green-800 font-medium rounded-lg text-sm px-5 py-2.5 text-center flex items-center justify-center" @click="addVariationToCart()">
                                <x-fas-cart-arrow-down class="w-6 h-6 mr-2" />
                                <span class="text-md">В корзину</span>
                            </button>
                            @if ($variation->is_constructable)
                                <a href="{{ route('client.constructor', ['variation_id' => $variation->id]) }}" class="w-full text-white bg-gradient-to-r from-green-400 via-green-500 to-green-600 hover:bg-gradient-to-br focus:ring-4 focus:outline-none focus:ring-green-300 dark:focus:ring-green-800 font-medium rounded-lg text-sm px-5 py-2.5 text-center">Редактировать в конструкторе</a>
                            @endif
                        </div>
                    </div>
                    @if (count($deliveries) > 0)
                        <div class="bg-slate-50 p-4 rounded-xl flex flex-col gap-4">
                            @foreach($deliveries as $delivery)
                                <div class="grid grid-cols-6 items-start gap-2">
                                    <div class="text-green-600 p-2 rounded-lg bg-white">
                                        @switch($delivery->type)
                                            @case('map')
                                                <x-carbon-pin class="w-full" />
                                                @break
                                            @case('text')
                                                <x-carbon-delivery class="w-full" />
                                                @break
                                            @case('delivery_systems')
                                                <x-carbon-cics-system-group class="w-full" />
                                                @break
                                            @default

                                        @endswitch
                                    </div>
                                    <div class="col-span-5">
                                        <div class="text-md text-slate-700 font-bold mb-1">
                                            {{ $delivery->name }}
                                        </div>
                                        @if ($delivery->type == 'map')

                                            @if($delivery->points)
                                                <div class="text-sm text-slate-600">
                                                    {{ explode("|", $delivery->points)[0] }}
                                                </div>
                                            @endif
                                        @endif
                                        <div class="text-xs text-slate-500 font-semibold">
                                            {{ $delivery->description }}
                                        </div>


                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

            </div>



        </div>
    </div>



    <div class="py-8" x-data="{
        activeTab: 0,
    }">
        <ul class="flex flex-wrap text-sm font-medium text-center text-gray-500 dark:text-gray-400 mb-4">
            <li class="me-2">
                <span class="inline-block px-4 py-3 rounded-lg active" :class="activeTab == 0 ? 'text-white bg-blue-600' : 'hover:text-gray-900 hover:bg-gray-100 dark:hover:bg-gray-800 dark:hover:text-white cursor-pointer'" aria-current="page" @click="activeTab = 0">Технические характеристики</span>
            </li>
            <li class="me-2">
                <span class="inline-block px-4 py-3 rounded-lg active" :class="activeTab == 1 ? 'text-white bg-blue-600' : 'hover:text-gray-900 hover:bg-gray-100 dark:hover:bg-gray-800 dark:hover:text-white cursor-pointer'" aria-current="page" @click="activeTab = 1">Подробное описание</span>
            </li>
            @if (!empty($files))
                <li class="me-2">
                    <span class="inline-block px-4 py-3 rounded-lg active" :class="activeTab == 2 ? 'text-white bg-blue-600' : 'hover:text-gray-900 hover:bg-gray-100 dark:hover:bg-gray-800 dark:hover:text-white cursor-pointer'" aria-current="page" @click="activeTab = 2">Файлы</span>
                </li>
            @endif
        </ul>
        <div class="p-6 bg-gray-50 text-medium text-gray-500 dark:text-gray-400 dark:bg-gray-800 rounded-lg w-full" x-show="activeTab == 0">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Технические характеристики</h3>
            <dl class="grid grid-cols-1 gap-4 md:grid-cols-2">
                @foreach($variation->paramItems as $paramItem)
                    <li class="flex items-center justify-between text-sm gap-2">
                        <strong class="font-medium text-slate-500">{{ $paramItem->productParam->name }}</strong>
                        <span class="grow border-b border-slate-300 border-dashed"></span>
                        <span class="font-medium">{{ $paramItem->title }}</span>
                    </li>
                @endforeach
                @foreach($variation->parametrs as $parametr)
                    <li class="flex items-center justify-between text-sm gap-2">
                        <strong class="font-medium text-slate-500">{{ $parametr->productParam->name }}</strong>
                        <span class="grow border-b border-slate-300 border-dashed"></span>
                        <span class="font-medium">{{ $parametr->title }}</span>
                    </li>
                @endforeach
            </dl>
        </div>
        <div class="p-6 bg-gray-50 text-medium text-gray-500 dark:text-gray-400 dark:bg-gray-800 rounded-lg w-full" x-show="activeTab == 1">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Подробное описание</h3>
            {!! nl2br(str($variation->description)->sanitizeHtml()) !!}
        </div>
        @if (!empty($files))
            <div class="p-6 bg-gray-50 text-medium text-gray-500 dark:text-gray-400 dark:bg-gray-800 rounded-lg w-full" x-show="activeTab == 2">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Файлы</h3>
                <ul role="list" class="divide-y divide-gray-100 rounded-md border border-gray-200">
                    @foreach($files as $key=>$file)
                        <li class="flex items-center justify-between py-4 pr-5 pl-4 text-sm/6">
                            <div class="flex w-0 flex-1 items-center">
                                <x-fas-file-import class="size-5 shrink-0 text-gray-400" />
                                <div class="ml-4 flex min-w-0 flex-1 gap-2">
                                    <span class="truncate font-bold">{{ $file['name'] }}</span>
                                    <span class="truncate font-medium">{{ File::basename(Storage::disk(config('filesystems.default'))->url($file['file'])) }}</span>
                                    <span class="shrink-0 text-gray-400">{{ $variation->formatBytes(Storage::disk(config('filesystems.default'))->size($file['file'])) }}</span>
                                </div>
                            </div>
                            <div class="ml-4 shrink-0">
                                {{-- <a href="{{ Storage::disk(config('filesystems.default'))->url($file['file']) }}" class="font-medium text-indigo-600 hover:text-indigo-500 cursor-pointer" download="{{ File::basename(Storage::disk(config('filesystems.default'))->url($file['file'])) }}">Скачать</a> --}}
                                <div class="font-medium text-indigo-600 hover:text-indigo-500 cursor-pointer" @click="downloadFile({{ $key }})">Скачать</div>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>


    @if (!empty($variation->upSells))
        @livewire('product.components.crossails', ['title' => 'С этим товаром покупают', 'variations' => $variation->upSells], key($variation->id))
    @endif

    @if (!empty($variation->crossSells))
        @livewire('product.components.crossails', ['title' => 'Похожие товары', 'variations' => $variation->crossSells], key($variation->id + rand(1,100)))
    @endif


</div>
