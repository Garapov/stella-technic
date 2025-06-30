@aware(['page'])
<div>
    @if (count($news))
        <section class="py-10 bg-slate-50 dark:bg-gray-800 glide" x-data="{
            slider: new window.glide($refs.slider, {
                autoplay: 5000,
                perView: 3,
                gap: 20,
                bound: true
            }).mount(),
            index: 0,
            init() {
                this.slider.on('move.after', () => {
                    this.index = this.slider.index;
                })
            },
        }" x-ref="slider">
            <div class="container mx-auto">
                <div class="flex items-center justify-between mb-10">
                    <p class="lg:text-4xl text-xl text-slate-900 dark:text-white font-semibold">{{ $title }}</p>
                    <div class="flex items-center gap-8">
                        <div class="flex items-center gap-2" data-glide-el="controls[nav]">
                            @foreach ($news as $key=>$item)
                                @if ($key > count($news) - 3)
                                    @continue
                                @endif
                                <div class="h-2.5 rounded-full transition-width" :class="{'w-6 bg-blue-400': index == {{ $key }}, 'w-2.5 bg-gray-400': index != {{ $key }} }" data-glide-dir="={{ $key }}"></div>
                            @endforeach
                        </div>
                        @if ($mainlink)
                            <a href="#"
                                class="inline-flex items-center font-medium text-blue-500 hover:underline">
                                Смотреть все
                                <x-fas-arrow-right class="w-4 h-4 ms-2" />
                            </a>
                        @endif
                    </div>
                </div>
                <div class="glide__track" data-glide-el="track">
                    <ul class="glide__slides">
                        @foreach ($news as $item)    
                            <div class="oglide__slide verflow-hidden bg-white rounded-lg border border-gray-200 shadow-md dark:bg-gray-800 dark:border-gray-700">
                                <img class="lg:h-48 md:h-36 w-full object-cover object-center" src="{{$item->banner_url}}"
                                    alt="blog">

                                    
                                <div class="p-6">
                                    <h2 class="tracking-widest text-xs title-font font-medium text-gray-400 mb-1">{{$item->category->name}}</h2>
                                    <h1 class="title-font text-lg font-medium text-blue-500 mb-3 whitespace-normal">{{$item->title}}</h1>
                                    <p class="leading-relaxed mb-3 dark:text-gray-200 text-gray-900 whitespace-normal">{{$item->excerpt}}</p>
                                    <div class="flex items-center flex-wrap ">
                                        <a href="{{ route('client.posts.show', ['category_slug' => $item->category->slug, 'slug' => $item->slug]) }}" class="text-indigo-500 inline-flex items-center md:mb-2 lg:mb-0" wire:navigate>Читать далее
                                            <svg class="w-4 h-4 ml-2" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"
                                                fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M5 12h14"></path>
                                                <path d="M12 5l7 7-7 7"></path>
                                            </svg>
                                        </a>
                                        <span
                                            class="text-gray-400 inline-flex items-center lg:ml-auto md:ml-0 ml-auto leading-none text-sm py-1">
                                            <svg class="w-4 h-4 mr-1" stroke="currentColor" stroke-width="2" fill="none"
                                                stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                                <circle cx="12" cy="12" r="3"></circle>
                                            </svg>1.2K
                                        </span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                        
                    </ul>
                </div>
            </div>
        </section>
    @endif
</div>

