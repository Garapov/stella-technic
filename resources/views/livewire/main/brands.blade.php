<div>
    @if (count($brands))
        <section class="bg-slate-50" x-data="brands" id="brands-slider">
            <div class="py-10 xl:px-[100px] px-[20px]">
                <div class="flex items-center justify-between mb-10">
                    <p class="lg:text-4xl text-xl text-slate-900 dark:text-white font-semibold">Наши бренды</p>
                    <div class="flex items-center gap-8">
                        <div class="lg:flex hidden items-center gap-2" data-glide-el="controls">
                            @foreach ($brands as $key=>$brand)
                                @if ($key > count($brands) - 6)      
                                    @continue
                                @endif
                                <div class="h-2.5 rounded-full transition-width" :class="{'w-6 bg-blue-400': index == {{ $key }}, 'w-2.5 bg-gray-400': index != {{ $key }} }" data-glide-dir="={{ $key }}"></div>
                            @endforeach
                        </div>
                        <div class="flex items-center gap-2" data-glide-el="controls">
                            <button type="button" class="text-white bg-blue-500 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm p-2 text-center inline-flex items-center dark:bg-blue-500 dark:hover:bg-blue-500 dark:focus:ring-blue-800" data-glide-dir="<">
                                <x-fas-arrow-left-long class="w-4 h-4" />
                            </button>
                            <button type="button" class="text-white bg-blue-500 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm p-2 text-center inline-flex items-center dark:bg-blue-500 dark:hover:bg-blue-500 dark:focus:ring-blue-800" data-glide-dir=">">
                                <x-fas-arrow-right-long class="w-4 h-4" />
                            </button>
                        </div>
                    </div>
                </div>
                <div class="flex justify-between gap-8 sm:gap-12 md:grid-cols-3 lg:grid-cols-6 text-gray-400">
                <div class="glide">
                    <div class="glide__track" data-glide-el="track">
                      <ul class="glide__slides items-stretch">
                        
                        @foreach ($brands as $brand)
                            <li class="glide__slide h-auto">
                                <div class="flex justify-center items-center h-full p-4 rounded bg-white">
                                    <img src="{{ Storage::disk(config('filesystems.default'))->url($brand->image) }}" alt="">    
                                </div>
                            </li>
                        
                        @endforeach
                      </ul>
                    </div>
                  </div>
                
                </div>
            </div>
        </section>
    @endif
</div>