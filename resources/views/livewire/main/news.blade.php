<div>
    @if (count($news))
        <section class="py-10 dark:bg-gray-800 glide" x-data="{
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
                    <p class="text-4xl text-slate-600 dark:text-white font-semibold">Новости</p>
                    <div class="flex items-center gap-8">
                        <div class="flex items-center gap-2" data-glide-el="controls[nav]">
                            @foreach ($news as $key=>$item)
                                @if ($key > count($news) - 3)
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
                        @foreach ($news as $item)    
                            @livewire('general.post', [
                                'post' => $item,
                            ], key($item->id))
                        @endforeach
                        
                    </ul>
                </div>
            </div>
        </section>
    @endif
</div>
