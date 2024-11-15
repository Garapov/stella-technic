<div>
    @if (count($clients))
        <section class="bg-gray-900" x-data="clients">
            <div class="container py-10 mx-auto">
                <div class="flex items-center justify-between mb-10">
                    <p class="text-4xl text-white">Наши клиенты</p>
                    <div class="flex items-center gap-8">
                        <div class="flex items-center gap-2">
                            <span class="w-6 h-2.5 bg-blue-600 rounded-full"></span>
                            <span class="w-2.5 h-2.5 bg-white rounded-full"></span>
                            <span class="w-2.5 h-2.5 bg-white rounded-full"></span>
                            <span class="w-2.5 h-2.5 bg-white rounded-full"></span>
                            <span class="w-2.5 h-2.5 bg-white rounded-full"></span>
                            <span class="w-2.5 h-2.5 bg-white rounded-full"></span>
                            <span class="w-2.5 h-2.5 bg-white rounded-full"></span>
                            <span class="w-2.5 h-2.5 bg-white rounded-full"></span>
                        </div>
                        <a href="#"
                            class="inline-flex items-center font-medium text-blue-500 hover:underline">
                            Смотреть все
                            <x-fas-arrow-right class="w-4 h-4 ms-2" />
                        </a>
                    </div>
                </div>
                <div class="flex justify-between gap-8 sm:gap-12 md:grid-cols-3 lg:grid-cols-6 text-gray-400">
                <div class="glide" id="clients-slider">
                    <div class="glide__track" data-glide-el="track">
                      <ul class="glide__slides">
                        
                        @foreach ($clients as $client)
                        <li class="glide__slide">
                            <a href="#" class="flex justify-center items-center opacity-50 hover:opacity-100">
                                <img src="{{ asset($client->image) }}" class="h-9" alt="">    
                            </a>
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