<div x-data="{
    product: @js($variation->product),
    variation: @js($variation),
    cart_quantity: 1,
    gallerySlider: null,
    thumbnailSlider: null,
    activeTab: 0,
    init() {
        $store.recently.toggleProduct({{ $variation->id }});
        if (document.querySelector('.gallery-slider')) {
            setTimeout(() => {
                    this.gallerySlider = new window.splide('.gallery-slider', {
                      {{-- type       : 'fade', --}}
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
                      perPage : 5,
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
            @seo(['title' => $variation->h1 ?? $variation->name])
            @if ($variation->short_description)
                @seo(['description' => $variation->short_description])
            @endif
            @if ($variation->gallery)
                @seo(['image' => Storage::disk(config('filesystems.default'))->url($variation->gallery[0])])
            @endif
        @endforelse
    @else
        @seo(['title' => $variation->h1 ?? $variation->name])
        @if ($variation->short_description)
            @seo(['description' => $variation->short_description])
        @endif
        @if ($variation->gallery)
            @seo(['image' => Storage::disk(config('filesystems.default'))->url($variation->gallery[0])])
        @endif
    @endif

    <h1 class="text-lg sm:text-3xl font-semibold text-slate-700 dark:text-white mb-4">{{ $variation->h1 ?? $variation->name }} {{ $variation->sku }}</h1>

    <div class="flex md:items-center md:flex-row flex-col gap-4 mb-4">
        <div class="flex items-center gap-2">
            Артикул: <span class="text-slate-500 font-semibold">{{ $variation->sku }}</span>
        </div>
        <div class="text-slate-500 hidden md:block">|</div>
        @if ($variation->product->brand)

            <div class="flex items-center gap-2">
                Бренд: <div class="flex items-center gap-1">
                    <img src="{{ Storage::disk(config('filesystems.default'))->url($variation->product->brand->image) }}" alt="{{ $variation->product->brand->name }}" class="h-6" />
                    {{-- <span class="text-slate-500 font-semibold">{{ $variation->product->brand->name }}</span> --}}
                </div>
            </div>

        @endif
    </div>

    <div class="grid grid-cols-9 gap-8">
        <div class="flex flex-col gap-8 md:col-span-7 col-span-full">
            <div class="grid items-start grid-cols-1 lg:grid-cols-7 gap-8 max-lg:gap-12 max-sm:gap-8">
                <div class="w-full lg:sticky top-10 lg:col-span-3 col-span-full" x-data="{
                    selector: `[data-fancybox='product_detail_page_gallery']`,
                    init() {
                        window.fancybox.bind(this.selector);
                        console.log(window.fancybox.bind(this.selector));
                    },
                }">
                    <div class="flex flex-col-reverse gap-4 p-4 rounded-lg shadow bg-white" x-ignore>
                        @if ($variation->gallery || $variation->videos)
                            <div class="grid grid-cols-8 gap-2">
                                <section class="splide gallery-thumbnails !invisible pointer-events-none md:pointer-events-auto md:!visible absolute md:relative" aria-label="Splide Basic HTML Example" ref="gallerySlider">
                                    <div class="h-full flex felx-col justify-center">
                                        <div class="w-full splide__track vertical-carousel-height-fix">
                                            <ul class="splide__list">
                                                @if ($variation->videos)
                                                    @foreach ($variation->videos as $video)
                                                        <li class="splide__slide"> <img src="{{ asset('assets/video-icon.jpg') }}"class="w-full  aspect-[1/1] object-cover"  /></li>
                                                    @endforeach
                                                @endif

                                                @if ($variation->gallery)
                                                    @foreach ($variation->gallery as $image)
                                                        <li class="splide__slide"> <img src="{{ Storage::disk(config('filesystems.default'))->url($image) }}" alt="Product1" class="w-full  aspect-[1/1] object-cover"  /></li>
                                                    @endforeach
                                                @endif

                                                @if ($variation->is_constructable && $variation->rows)
                                                    <li class="splide__slide">
                                                        <img src="{{ asset('assets/3dicon.png') }}" alt="3dicon" class="w-full  aspect-[1/1] object-cover"  />
                                                    </li>
                                                @endif
                                            </ul>
                                        </div>
                                    </div>
                                </section>
                                <section class="splide gallery-slider md:col-span-7 col-span-full" aria-label="Splide Basic HTML Example" ref="gallerySlider">
                                    <div class="splide__track">
                                        <ul class="splide__list">
                                            @if ($variation->videos)
                                                @foreach ($variation->videos as $video)
                                                    <li class="splide__slide relative">
                                                        <video src="{{ Storage::disk(config('filesystems.default'))->url($video) }}" alt="Product1" class="w-full  aspect-[1/1] object-cover"> </video>
                                                        <span class="block absolute inset-48 flex items-center justify-center text-white bg-blue-700 rounded-lg opacity-100 hover:opacity-75 transition-opacity cursor-pointer" data-fancybox="product_detail_page_gallery" data-src="{{ Storage::disk(config('filesystems.default'))->url($video) }}">
                                                            <svg class="w-12 h-12" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                            <path d="M8 5v14l11-7z"></path>
                                                            </svg>
                                                        </span>
                                                    </li>
                                                @endforeach
                                            @endif
                                            @if ($variation->gallery)
                                                @foreach ($variation->gallery as $image)
                                                    <li class="splide__slide" data-fancybox="product_detail_page_gallery" data-src="{{ Storage::disk(config('filesystems.default'))->url($image) }}"> <img src="{{ Storage::disk(config('filesystems.default'))->url($image) }}" alt="Product1" class="w-full  aspect-[1/1] object-cover"  /></li>
                                                @endforeach
                                            @endif

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

                <div class="w-full lg:sticky top-10 lg:col-span-4 col-span-full">
                    <x-product.panel :variation="$variation" :features="$features" />
                </div>
                <div class="md:col-span-3 col-span-full md:hidden block">
                    <div class="flex flex-col items-start gap-4">
                        <x-product.params :variation="$variation" :groupedParams="$groupedParams" />
                    </div>
                </div>
            </div>

            <div class="pt-8">

                <ul class="flex flex-wrap text-sm font-medium text-center text-gray-500 border-b border-gray-100 dark:border-gray-700 dark:text-gray-400">
                    <li class="me-2" id="params">
                        <span aria-current="page" class="inline-block p-4 rounded-t-lg" :class="activeTab == 0 ? 'active bg-blue-600 text-white' : 'text-gray-600 bg-white shadow cursor-pointer'" aria-current="page" @click="activeTab = 0">Технические характеристики</span>
                    </li>
                    @if ($variation->description)
                        <li class="me-2">
                            <span aria-current="page" class="inline-block p-4 rounded-t-lg" :class="activeTab == 1 ? 'active bg-blue-600 text-white' : 'text-gray-600 bg-white shadow cursor-pointer'" aria-current="page" @click="activeTab = 1">Подробное описание</span>
                        </li>
                    @endif
                    @if (!empty($files))
                        <li class="me-2">
                            <span aria-current="page" class="inline-block p-4 rounded-t-lg" :class="activeTab == 2 ? 'active bg-blue-600 text-white' : 'text-gray-600 bg-white shadow cursor-pointer'" aria-current="page" @click="activeTab = 2">Файлы</span>
                        </li>
                    @endif
                </ul>
                <div class="text-medium rounded-b-lg bg-white shadow w-full p-4" x-show="activeTab == 0">
                    <div class="grid grid-cols-1 gap-x-4 md:grid-cols-2 text-slate-700">
                        @foreach($variation->paramItems->merge($variation->parametrs)->where('is_hidden', false)->sortBy('productParam.sort')->split(2) as $paramItemGroup)
                            {{-- @if ($paramItem->productParam->is_hidden)
                                @continue
                            @endif --}}
                            <dl class="flex flex-col gap-4">
                                @foreach ($paramItemGroup as $paramItem)
                                    <li class="flex items-center justify-between text-sm gap-2 px-3 py-2">
                                        <strong class="font-medium">{{ $paramItem->productParam->name }}</strong>
                                        <span class="grow border-b border-gray-200 border-dotted border-b-2"></span>
                                        <span class="font-bold">{{ $paramItem->title }}</span>
                                    </li>
                                @endforeach
                            </dl>
                        @endforeach
                    </div>
                </div>
                @if ($variation->description)
                    <div class="text-medium text-gray-900 bg-white shadow rounded-b-lgw-full py-8 px-10 content_block flex flex-col gap-4" x-show="activeTab == 1">
                        <!-- <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Подробное описание</h3> -->
                        {!! str($variation->description)->sanitizeHtml() !!}
                    </div>
                @endif
                @if (!empty($files))
                    <div class="text-medium text-gray-500 bg-white shadow w-full" x-show="activeTab == 2">
                        <!-- <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Файлы</h3> -->
                        <ul role="list" class="divide-y divide-gray-100 rounded-b-lg">
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

        </div>

        <div class="md:col-span-2 col-span-full hidden md:block">
            <div class="md:sticky top-20">
                <x-product.params :variation="$variation" :groupedParams="$groupedParams" />


                @if (count($deliveries) > 0)
                    {{-- <div class="bg-slate-50 p-4 rounded-lg flex flex-col gap-4 mt-4">
                        @foreach($deliveries as $delivery)
                            <div class="grid grid-cols-8 items-start gap-4">
                                <div class="col-span-2 text-green-600 p-2 rounded-lg bg-white">
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
                                <div class="col-span-6">
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

                            <div class="flex flex-col gap-4">

                            </div>
                        @endforeach
                    </div>--}}



                    <div class="border border-b-0 border-gray-200 bg-slate-50 rounded-lg flex flex-col mt-4 overflow-hidden" x-data="{
                        openedTab: 0,
                        openTab(index) {
                            this.openedTab = index;
                        }
                    }">
                        @foreach($deliveries as $key => $delivery)
                            <h2>
                                <button type="button" class="flex items-center justify-between w-full p-5 font-medium rtl:text-right text-gray-500 focus:ring-4 focus:ring-gray-200 hover:bg-gray-100 gap-3" @click="openTab({{ $key }})" :class="{'border border-x-0 border-t-0 border-gray-200': openedTab != {{ $key }}}">
                                    <span class="flex items-center">
                                        <svg class="w-5 h-5 me-2 shrink-0" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"></path>
                                        </svg> {{ $delivery->name }}
                                    </span>
                                    <svg class="w-3 h-3 rotate-180 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5 5 1 1 5"/>
                                    </svg>
                                </button>
                            </h2>
                            <div :class="{'hidden': openedTab != {{ $key }}}">
                                <div class="p-5 border border-x-0 border-gray-200" :class="{'bg-white': openedTab == {{ $key }}}">
                                    @if ($delivery->type == 'map')

                                        @if($delivery->points)
                                            <div class="text-sm text-slate-600">
                                                {{ explode("|", $delivery->points)[0] }}
                                            </div>
                                        @endif
                                        
                                    @endif
                                    <div class="text-xs text-gray-500 font-semibold">
                                        {!! $delivery->text !!}
                                        @if($delivery->images)
                                            <div class="grid grid-cols-2 gap-4 md:grid-cols-3 lg:grid-cols-4">
                                                @foreach ($delivery->images as $image)
                                                    <div class="rounded-lg overflow-hidden">
                                                        <img src="{{ Storage::disk(config('filesystems.default'))->url($image) }}" class="w-full h-full object-cover">
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>



    @php
        $paramItemIds = $variation->paramItems->merge($variation->parametrs)->filter(fn($param) => $param->productParam->is_for_crossail)->pluck('id');
        $crossSellsVariants = \App\Models\ProductVariant::whereHas('paramItems', function ($query) use ($paramItemIds) {
            $query->whereIn('product_param_items.id', $paramItemIds);
        })
        ->orWhereHas('parametrs', function ($query) use ($paramItemIds) {
            $query->whereIn('product_param_items.id', $paramItemIds);
        })
        ->get()
        ->filter(function ($variant) use ($paramItemIds) {
            $allParamItems = $variant->paramItems->merge($variant->parametrs)->pluck('id')->unique();

            // Проверяем, что все элементы из $paramItemIds есть в $allParamItems
            return collect($paramItemIds)->every(function ($id) use ($allParamItems) {
                return $allParamItems->contains($id);
            });
        });
    @endphp

    @if ($crossSellsVariants || count($variation->upSells) > 0)
        @livewire('product.components.crossails', ['title' => 'С этим товаром покупают', 'variations' => $variation->upSells->merge($crossSellsVariants)], key($variation->id))
    @endif

    @if ($variation->product->variants || count($variation->crossSells) > 0)
        @livewire('product.components.crossails', ['title' => 'Похожие товары', 'variations' => $variation->crossSells->merge($variation->product->variants->where('id', '!=', $variation->id))->take(10)], key($variation->id + rand(1,100)))
    @endif

    @livewire('general.recently', key($variation->id))

    
</div>
