<div>
    @if (count($variations) > 0)
        <section class="py-10 dark:bg-dark" x-data="{
            slider: new window.glide($refs.slider, {
                autoplay: 5000,
                perView: 4,
                gap: 20,
                bound: true,
                breakpoints: {
                    1024: {
                        perView: 2
                    },
                    800: {
                        perView: 1
                    }
                }
            }).mount(),
            index: 0,
            init() {
                this.slider.on('move.after', () => {
                    this.index = this.slider.index;
                })
            },
        }">
            <div class="lg:container lg:mx-auto" x-ref="slider">
                <div class="flex items-center justify-between mb-10">
                    <p class="lg:text-4xl text-xl text-slate-900 dark:text-white font-semibold">{{ $title }}</p>
                    <div class="flex items-center gap-8">
                        <div class="flex items-center gap-2" data-glide-el="controls[nav]">
                            @foreach ($variations as $key=>$variation)
                                @if ($key > count($variations) - 4)
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
                    {{-- <div class="flex items-center gap-8">
                        <a href="{{ route('client.catalog.popular') }}"
                            class="inline-flex items-center font-medium text-blue-600 dark:text-blue-500 hover:underline" wire:navigate>
                            Смотреть все
                            <x-fas-arrow-right class="w-4 h-4 ms-2 rtl:rotate-180" />
                        </a>
                    </div> --}}
                </div>
                <div class="glide__track" data-glide-el="track">
                    <div class="glide__slides whitespace-normal">

                        @foreach ($variations as $variation)
                            @livewire('general.product-variant', [
                                'variant' => $variation
                            ], key($variation->id))
                        @endforeach
                    </div>
                </div>
            </div>
        </section>
    @endif
</div>
