<div class="w-full lg:sticky top-10 lg:col-span-3 col-span-full" x-data="{
    selector: `[data-fancybox='product_detail_page_gallery']`,
    gallerySlider: null,
    thumbnailSlider: null,
    init() {
        window.fancybox.bind(this.selector);

        
        if (document.querySelector('.gallery-slider') && this.gallerySlider === null) {
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
                    heightRatio: 1,
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
            }
    },
}">
    <div class="flex flex-col-reverse gap-4 p-4 rounded-lg shadow bg-white" x-ignore>
        @if ($this->images || $this->videos)
            <div class="grid grid-cols-8 gap-2">
                <section class="splide gallery-thumbnails !invisible pointer-events-none md:pointer-events-auto md:!visible absolute md:relative" aria-label="Splide Basic HTML Example" ref="gallerySlider">
                    <div class="h-full flex felx-col justify-center">
                        <div class="w-full splide__track vertical-carousel-height-fix">
                            <ul class="splide__list">
                                @if ($this->videos)
                                    @foreach ($this->videos as $video)
                                        <li class="splide__slide"> <img src="{{ asset('assets/video-icon.jpg') }}"class="w-full  aspect-[1/1] object-cover"  /></li>
                                    @endforeach
                                @endif

                                @if ($this->images)
                                    @foreach ($this->images as $image)
                                        <li class="splide__slide"> <img src="{{ Storage::disk(config('filesystems.default'))->url($image) }}" alt="Product1" class="w-full  aspect-[1/1] object-cover"  /></li>
                                    @endforeach
                                @endif

                                @if ($variation->is_constructable && $this->rows)
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
                            @if ($this->videos)
                                @foreach ($this->videos as $video)
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
                            @if ($this->images)
                                @foreach ($this->images as $image)
                                    <li class="splide__slide" data-fancybox="product_detail_page_gallery" data-src="{{ Storage::disk(config('filesystems.default'))->url($image) }}"> <img src="{{ Storage::disk(config('filesystems.default'))->url($image) }}" alt="Product1" class="w-full  aspect-[1/1] object-cover"  /></li>
                                @endforeach
                            @endif

                            @if ($variation->is_constructable && $this->rows)
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