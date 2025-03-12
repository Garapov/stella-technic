<div x-data="{
    product: @js($variation->product),
    variation: @js($variation),
    gallery: @js($gallery),
    cart_quantity: 1,
    addVariationToCart: function () {
        $store.cart.addVariationToCart({
            product: this.product,
            count: this.cart_quantity,
            variation: this.variation
        });
    }
}">

    <div class="grid items-start grid-cols-1 lg:grid-cols-2 gap-8 max-lg:gap-12 max-sm:gap-8">
        <div class="w-full lg:sticky top-0">
            <div class="flex flex-row gap-2">
                <div class="flex flex-col gap-2 w-16 max-sm:w-14 shrink-0">
                    <img src="{{ asset('/storage/' . $variation->img->uuid .'/filament-thumbnail.' . $variation->img->file_extension) }}" alt="Product1"
                        class="aspect-[1/1] object-cover object-top w-full cursor-pointer  border-b-2 border-black" />
                    
                </div>
                <div class="flex-1">
                    <img src="{{ asset('/storage/' . $variation->img->uuid .'/original.' . $variation->img->file_extension) }}" alt="Product1"
                        class="w-full  aspect-[1/1] object-cover"  />
                </div>
            </div>
        </div>

        <div class="w-full">
            <div>
                <h1 class="text-lg sm:text-xl font-semibold text-slate-900">{{ $variation->name }}</h1>
                <div class="flex items-center flex-wrap gap-4 mt-6">
                    <h4 class="text-slate-900 text-2xl sm:text-3xl font-semibold">{{ $variation->new_price ? Number::format($variation->new_price, 0) : Number::format($variation->price, 0) }} ₽</h4>
                    @if ($variation->new_price)
                        <p class="text-slate-500 text-lg"><strike>{{Number::format($variation->price, 0)}} ₽</strike></p>
                    @endif
                </div>
            </div>

            <hr class="my-6 border-slate-300" />

            <div>
                <div class="parametrs">
                    @foreach($groupedParams as $paramGroup)
                        <div class="mb-6">
                            <h3 class="text-lg sm:text-xl font-semibold text-slate-900">{{ $paramGroup['name'] }}</h3>
                            <div class="flex flex-wrap gap-4 mt-4">
                                @foreach($paramGroup['values'] as $value)
                                    <a href="{{ route('client.product_detail', $variation->product->variants->where('id', $value['variant_id'])->first()->slug) }}"
                                        @class([
                                            'px-4 h-9 border text-sm flex items-center justify-center shrink-0',
                                            'border-blue-600 bg-blue-50' => $value['is_current'],
                                            'border-slate-300 hover:border-blue-600' => !$value['is_current'] && $value['is_available'],
                                            'border-slate-200 bg-slate-50 text-slate-400' => !$value['is_available']
                                        ]) wire:navigate >
                                        {{ $value['title'] }}
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-6 flex flex-wrap gap-4">
                    <button type="button"
                        class="px-4 py-3 w-[45%] border border-slate-300 bg-slate-100 hover:bg-slate-200 text-slate-900 text-sm font-medium">Добавить в избранное</button>
                    <button type="button"
                        class="px-4 py-3 w-[45%] border border-blue-600 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium" @click="addVariationToCart()">В корзину</button>
                </div>
            </div>

            <hr class="my-6 border-slate-300" />

            <div>
                <h3 class="text-lg sm:text-xl font-semibold text-slate-900">Информация о товаре</h3>
                <div class="mt-4" role="accordion">
                    <div class="hover:bg-slate-100 transition-all" x-data="{
                            isOpened: true
                        }">
                        <button type="button"
                            class="w-full text-sm font-semibold text-left px-4 py-2.5 text-slate-900 flex items-center" @click="isOpened = !isOpened">
                            <span class="mr-4">Параметры товара</span>
                            <svg xmlns="http://www.w3.org/2000/svg"
                                class="w-3 h-3 fill-current ml-auto shrink-0" :class="{'-rotate-180': isOpened}" viewBox="0 0 24 24">
                                <path fill-rule="evenodd"
                                    d="M11.99997 18.1669a2.38 2.38 0 0 1-1.68266-.69733l-9.52-9.52a2.38 2.38 0 1 1 3.36532-3.36532l7.83734 7.83734 7.83734-7.83734a2.38 2.38 0 1 1 3.36532 3.36532l-9.52 9.52a2.38 2.38 0 0 1-1.68266.69734z"
                                    clip-rule="evenodd" data-original="#000000"></path>
                            </svg>
                        </button>
                        <div class="pb-4 px-4" x-show="isOpened">
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

                    <div class="hover:bg-slate-100 transition-all" x-data="{
                        isOpened: false
                    }">
                    <button type="button"
                        class="w-full text-sm font-semibold text-left px-4 py-2.5 text-slate-900 flex items-center" @click="isOpened = !isOpened">
                        <span class="mr-4">Подробное описание</span>
                        <svg xmlns="http://www.w3.org/2000/svg"
                            class="w-3 h-3 fill-current ml-auto shrink-0" :class="{'-rotate-180': isOpened}" viewBox="0 0 24 24">
                            <path fill-rule="evenodd"
                                d="M11.99997 18.1669a2.38 2.38 0 0 1-1.68266-.69733l-9.52-9.52a2.38 2.38 0 1 1 3.36532-3.36532l7.83734 7.83734 7.83734-7.83734a2.38 2.38 0 1 1 3.36532 3.36532l-9.52 9.52a2.38 2.38 0 0 1-1.68266.69734z"
                                clip-rule="evenodd" data-original="#000000"></path>
                        </svg>
                    </button>
                    <div class="pb-4 px-4" x-show="isOpened">
                        {!! str($variation->description)->sanitizeHtml() !!}
                    </div>
                </div>
                </div>
            </div>
        </div>
    </div>
</div>
