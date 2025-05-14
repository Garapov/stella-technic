<div>
    @if (count($categories) > 0)
        <section class="py-10 dark:bg-dark overflow-hidden" x-data="{
            slider: new window.glide($refs.slider, {
                autoplay: 5000,
                perView: 5,
                gap: 10,
                bound: true
            }).mount(),
            index: 0,
            init() {
                this.slider.on('move.after', () => {
                    this.index = this.slider.index;
                })
            },
        }">
            <div class="container mx-auto glide" x-ref="slider">
                <div class="flex items-center justify-between mb-10">
                    <p class="text-4xl text-slate-600 dark:text-white font-semibold">Популярные категории</p>
                    <div class="flex items-center gap-6">
                        <div class="flex items-center gap-2" data-glide-el="controls">
                            @foreach ($categories as $key=>$category)
                                @if ($key > count($categories) - 8)      
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
                <div class="glide__track overflow-visible" data-glide-el="track">
                    <div class="glide__slides overflow-visible align-stretch">
                        @foreach ($categories as $category)
                            @if ($category->products->count() == 0)
                                @continue
                            @endif
                            @livewire('general.category', [
                                'category' => $category,
                            ], key($category->id))
                        @endforeach
                    </div>
                </div>
            </div>
        </section>
    @endif
</div>