<div x-data="{
    product: @js($variation->product),
    variation: @js($variation),
    cart_quantity: 1,
    gallerySlider: new window.splide('.gallery-slider', {
      type       : 'fade',
      heightRatio: 1,
      pagination : true,
      arrows     : false,
      cover      : true,
      rewind          : true,
    }),
    thumbnailSlider: new window.splide('.gallery-thumbnails', {
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
    }),
    init() {
        setTimeout(() => {
            this.gallerySlider.sync( this.thumbnailSlider );
            this.gallerySlider.mount();
            this.thumbnailSlider.mount();
        }, 0)
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
            <div class="flex flex-col-reverse gap-4">
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

            <div class="grid grid-cols-2 gap-4">

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
                    <div class="flex items-center flex-wrap justify-end gap-4">
                        <h4 class="text-slate-900 text-4xl font-semibold">{{ $variation->new_price ? Number::format($variation->new_price, 0) : Number::format($variation->price, 0) }} ₽</h4>
                        @if ($variation->new_price)
                            <p class="text-slate-500 text-lg"><strike>{{Number::format($variation->price, 0)}} ₽</strike></p>
                        @endif
                    </div>

                    <div class="flex flex-wrap items-start gap-4">

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
                                class="rounded-lg p-2 text-gray-500 hover:bg-gray-100 hover:text-gray-900 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white">
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
                        <button type="button" class="grow text-white bg-gradient-to-r from-blue-500 via-blue-600 to-blue-700 hover:bg-gradient-to-br focus:ring-4 focus:outline-none focus:ring-blue-300 dark:focus:ring-blue-800 font-medium rounded-lg px-5 py-2.5 text-center flex items-center justify-center text-md" @click="addVariationToCart()">
                            <x-fas-cart-arrow-down class="w-5 h-5 mr-2" />
                            В корзину
                        </button>
                        @if ($variation->is_constructable)
                            <a href="{{ route('client.constructor', ['variation_id' => $variation->id]) }}" class="w-full text-white bg-gradient-to-r from-green-400 via-green-500 to-green-600 hover:bg-gradient-to-br focus:ring-4 focus:outline-none focus:ring-green-300 dark:focus:ring-green-800 font-medium rounded-lg text-sm px-5 py-2.5 text-center me-2 mb-2">Редактировать в конструкторе</a>
                        @endif
                    </div>
                </div>

            </div>



            <hr class="my-6 border-slate-300" />


            <div class="rounded-lg border border-slate-200 overflow-hidden bg-slate-50">
                <div class="dark:border-neutral-600 dark:bg-body-dark border-b border-slate-200" x-data="{
                    isOpened: true
                }">
                    <button class="group relative flex w-full items-center rounded-t-lg border-0 px-5 py-4 text-left text-base text-neutral-800 transition [overflow-anchor:none] hover:z-[2] focus:z-[3] focus:outline-none dark:bg-body-dark dark:text-white font-bold" type="button"  @click="isOpened = !isOpened">
                        Информация о товаре
                        <span class="-me-1 ms-auto h-5 w-5 shrink-0 rotate-[-180deg] transition-transform duration-200 ease-in-out group-data-[twe-collapse-collapsed]:me-0 group-data-[twe-collapse-collapsed]:rotate-0 motion-reduce:transition-none [&>svg]:h-6 [&>svg]:w-6">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24" :class="{'-rotate-180': !isOpened, 'rotate-0': isOpened}"><g data-name="arrow-ios-downward"><path d="M12 16a1 1 0 0 1-.64-.23l-6-5a1 1 0 1 1 1.28-1.54L12 13.71l5.36-4.32a1 1 0 0 1 1.41.15 1 1 0 0 1-.14 1.46l-6 4.83A1 1 0 0 1 12 16z"></path></g></svg>
                        </span>
                    </button>
                    <div class="px-5 py-4" x-show="isOpened">
                        <dl class="grid grid-cols-1 gap-4 md:grid-cols-2">
                            @foreach($variation->paramItems as $paramItem)
                                <li class="flex flex-center justify-between text-xs">
                                    <span>{{ $paramItem->productParam->name }}</span>
                                    <span>{{ $paramItem->title }}</span>
                                </li>
                            @endforeach
                            @foreach($variation->parametrs as $parametr)
                                <li class="flex flex-center justify-between text-xs">
                                    <span>{{ $parametr->productParam->name }}</span>
                                    <span>{{ $parametr->title }}</span>
                                </li>
                            @endforeach
                        </dl>
                    </div>
                </div>



                <div class="dark:border-neutral-600 dark:bg-body-dark" x-data="{
                    isOpened: false
                }">
                    <button class="group relative flex w-full items-center rounded-t-lg border-0 px-5 py-4 text-left text-base text-neutral-800 transition [overflow-anchor:none] hover:z-[2] focus:z-[3] focus:outline-none dark:bg-body-dark dark:text-white font-bold" type="button"  @click="isOpened = !isOpened">
                        Подробное описание
                        <span class="-me-1 ms-auto h-5 w-5 shrink-0 rotate-[-180deg] transition-transform duration-200 ease-in-out group-data-[twe-collapse-collapsed]:me-0 group-data-[twe-collapse-collapsed]:rotate-0 motion-reduce:transition-none [&>svg]:h-6 [&>svg]:w-6">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24" :class="{'-rotate-180': !isOpened, 'rotate-0': isOpened}"><g data-name="arrow-ios-downward"><path d="M12 16a1 1 0 0 1-.64-.23l-6-5a1 1 0 1 1 1.28-1.54L12 13.71l5.36-4.32a1 1 0 0 1 1.41.15 1 1 0 0 1-.14 1.46l-6 4.83A1 1 0 0 1 12 16z"></path></g></svg>
                        </span>
                    </button>
                    <div class="px-5 py-4" x-show="isOpened">
                        {!! str($variation->description)->sanitizeHtml() !!}
                    </div>
                </div>
            </div>

            
        </div>
    </div>
</div>
