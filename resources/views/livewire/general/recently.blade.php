<div>
    <section class="py-10 dark:bg-dark" x-data="{
        products: $wire.$entangle('variations'),
        isLoading: true,
        slider: new window.glide($refs.slider, {
            autoplay: 3000,
            perView: 5,
            gap: 20,
            bound: true,
            breakpoints: {
                1366: {
                    perView: 4
                },
                1280: {
                    perView: 3
                },
                1024: {
                    perView: 2
                },
                800: {
                    perView: 1
                }
            }
        }),
        index: 0,
        init() {
            this.slider.on('move.after', () => {
                this.index = this.slider.index;
            })
            this.loadVariations();
        },
        loadVariations() {
            $wire.loadProducts($store.recently.list).then((products) => {
                this.isLoading = false;
                this.slider.mount();
            });
        }
    }">
        <div class="" x-ref="slider">
            <div class="flex items-center justify-between mb-10">
                <p class="lg:text-4xl text-xl text-slate-900 dark:text-white font-semibold">Недавно просмотренные</p>
                <div class="flex items-center gap-8">
                    <div class="flex items-center gap-2" data-glide-el="controls[nav]">
                        @foreach ($variations as $key=>$variation)
                            @if ($key > count($variations) - 5)
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
</div>
