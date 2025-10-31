<div>
    @if (count($this->news))
    {!! $this->schema !!}
        <section class="px-4 py-10 dark:bg-gray-800 glide" x-data="{
            slider: new window.glide($refs.slider, {
                autoplay: 5000,
                perView: 5,
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
        }" x-ref="slider">
            <div class="xl:px-[100px] px-[20px]">
                <div class="flex items-center justify-between mb-10">
                    <p class="lg:text-4xl text-xl text-slate-900 dark:text-white font-semibold">Новости</p>
                    <div class="md:flex hidden items-center gap-8">
                        <div class="flex items-center gap-2" data-glide-el="controls[nav]">
                            @foreach ($this->news as $key=>$item)
                                @if ($key > count($this->news) - 3)
                                    @continue
                                @endif
                                <div class="h-2.5 rounded-full transition-width" :class="{'w-6 bg-blue-400': index == {{ $key }}, 'w-2.5 bg-gray-400': index != {{ $key }} }" data-glide-dir="={{ $key }}"></div>
                            @endforeach
                        </div>
                        <a href="{{ route('client.posts.index') }}"
                            class="inline-flex items-center font-medium text-blue-600 dark:text-blue-500 hover:underline" wire:navigate>
                            Смотреть все
                            <x-fas-arrow-right class="w-4 h-4 ms-2 rtl:rotate-180" />
                        </a>
                    </div>
                </div>
                <div class="glide__track" data-glide-el="track">
                    <ul class="glide__slides">
                        @foreach ($this->news as $item)    
                            @livewire('general.post', [
                                'post' => $item,
                            ], key($item->id))
                        @endforeach
                        
                    </ul>
                </div>
                <a href="{{ route('client.posts.index') }}"
                    class="md:hidden inline-flex items-center font-medium text-blue-600 dark:text-blue-500 hover:underline mt-4" wire:navigate>
                    Смотреть все
                    <x-fas-arrow-right class="w-4 h-4 ms-2 rtl:rotate-180" />
                </a>
            </div>
        </section>
    @endif
</div>
