<div x-data="{
    product: @js($variation->product),
    variation: @js($variation),
    cart_quantity: 1,
    gallerySlider: null,
    thumbnailSlider: null,
    activeTab: 0,
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
    
    <h1 class="text-lg sm:text-3xl font-semibold text-slate-700 dark:text-white mb-4">{{ $variation->name }}</h1>

    <div class="flex items-center gap-4 mb-4">
        <div class="flex items-center gap-2">
            Артикул: <span class="text-slate-500 font-semibold">{{ $variation->sku }}</span>
        </div>
        <div class="text-slate-500">|</div>
        @if ($variation->product->brand)
            
            <div class="flex items-center gap-2">
                Бренд: <div class="flex items-center gap-1">
                    <img src="{{ Storage::disk(config('filesystems.default'))->url($variation->product->brand->image) }}" alt="{{ $variation->product->brand->name }}" class="h-6" />
                    <span class="text-slate-500 font-semibold">{{ $variation->product->brand->name }}</span>
                </div>
            </div>
            
        @endif
    </div>

    <div class="grid grid-cols-9 gap-8">
        <div class="flex flex-col gap-8 md:col-span-7 col-span-full">
            <div class="grid items-start grid-cols-1 lg:grid-cols-6 gap-8 max-lg:gap-12 max-sm:gap-8">
                <div class="w-full lg:sticky top-10 lg:col-span-3 col-span-full">
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

                <div class="w-full lg:sticky top-10 lg:col-span-3 col-span-full">
                    <x-product.panel :variation="$variation" />
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
                        <span aria-current="page" class="inline-block p-4 rounded-t-lg" :class="activeTab == 0 ? 'active bg-gray-100 dark:bg-gray-800 dark:text-blue-50' : 'hover:text-gray-600 hover:bg-gray-50 dark:hover:bg-gray-800 dark:hover:text-gray-300 cursor-pointer'" aria-current="page" @click="activeTab = 0">Технические характеристики</span>
                    </li>
                    <li class="me-2">
                        <span aria-current="page" class="inline-block p-4 rounded-t-lg" :class="activeTab == 1 ? 'active bg-gray-100 dark:bg-gray-800 dark:text-blue-50' : 'hover:text-gray-600 hover:bg-gray-50 dark:hover:bg-gray-800 dark:hover:text-gray-300 cursor-pointer'" aria-current="page" @click="activeTab = 1">Подробное описание</span>
                    </li>
                    @if (!empty($files))
                        <li class="me-2">
                            <span aria-current="page" class="inline-block p-4 rounded-t-lg" :class="activeTab == 2 ? 'active bg-gray-100 dark:bg-gray-800 dark:text-blue-50' : 'hover:text-gray-600 hover:bg-gray-50 dark:hover:bg-gray-800 dark:hover:text-gray-300 cursor-pointer'" aria-current="page" @click="activeTab = 2">Файлы</span>
                        </li>
                    @endif

                    @if (count($deliveries) > 0)
                        <li class="me-2">
                            <span aria-current="page" class="inline-block p-4 rounded-t-lg" :class="activeTab == 3 ? 'active bg-gray-100 dark:bg-gray-800 dark:text-blue-50' : 'hover:text-gray-600 hover:bg-gray-50 dark:hover:bg-gray-800 dark:hover:text-gray-300 cursor-pointer'" aria-current="page" @click="activeTab = 3">Доставка</span>
                        </li>
                    @endif
                </ul>
                <div class="text-medium text-gray-500 dark:text-gray-400 dark:bg-gray-800 rounded-b-lg w-full p-4" x-show="activeTab == 0">
                    <!-- <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Технические характеристики</h3> -->
                    <dl class="grid grid-cols-1 gap-8 md:grid-cols-1">
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
                <div class="text-medium text-gray-500 dark:text-gray-400 dark:bg-gray-800 rounded-b-lgw-full p-4" x-show="activeTab == 1">
                    <!-- <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Подробное описание</h3> -->
                    {!! nl2br(str($variation->description)->sanitizeHtml()) !!}
                </div>
                @if (!empty($files))
                    <div class="text-medium text-gray-500 dark:text-gray-400 dark:bg-gray-800 w-full" x-show="activeTab == 2">
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
                
                @if (count($deliveries) > 0)
                    <div class="bg-gray-100 p-4 rounded-b-xl flex flex-col gap-4" x-show="activeTab == 3">
                        @foreach($deliveries as $delivery)
                            <div class="grid grid-cols-10 items-center gap-4">
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
                                <div class="col-span-7">
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

        <div class="md:col-span-2 col-span-full hidden md:block">
            <x-product.params :variation="$variation" :groupedParams="$groupedParams" />   
        </div>
    </div>


    @if (count($variation->upSells) > 0)
        @livewire('product.components.crossails', ['title' => 'С этим товаром покупают', 'variations' => $variation->upSells], key($variation->id))
    @endif

    @if (count($variation->crossSells) > 0)
        @livewire('product.components.crossails', ['title' => 'Похожие товары', 'variations' => $variation->crossSells], key($variation->id + rand(1,100)))
    @endif

    @livewire('general.forms.buyoneclick', ['variation' => $variation])
</div>
