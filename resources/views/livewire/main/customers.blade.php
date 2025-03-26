<div>
    @if (count($clients))
        <section class="bg-gray-900" x-data="clients" id="clients-slider">
            <div class="container py-10 mx-auto">
                <div class="flex items-center justify-between mb-10">
                    <p class="text-4xl text-white">Наши клиенты</p>
                    <div class="flex items-center gap-8">
                        <div class="flex items-center gap-2" data-glide-el="controls">
                            @foreach ($clients as $key=>$client)
                                @if ($key > count($clients) - 6)      
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
                        
                        @foreach ($clients as $client)
                            <li class="glide__slide h-auto">
                                <div class="flex justify-center items-center h-full p-4 rounded bg-white">
                                    <img src="{{ Storage::disk(config('filesystems.default'))->url($client->image) }}" alt="">    
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