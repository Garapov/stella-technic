@php
    $lightbox_selector = "lightgallery-" . substr(bin2hex(openssl_random_pseudo_bytes(10 / 2)), 0, 10);
@endphp
<div>
    @if (count($certificates))
        @if ($type == 'slider')
            <section class="px-4 py-10 bg-slate-50 dark:bg-gray-800 glide" x-data="{
                slider: new window.glide($refs.slider, {
                    autoplay: 5000,
                    perView: 6,
                    gap: 20,
                    bound: true,
                    breakpoints: {
                        1024: {
                            perView: 3
                        },
                        800: {
                            perView: 2
                        },
                        560: {
                            perView: 1
                        }
                    }
                }).mount(),
                selector: `[data-fancybox='{{ $lightbox_selector }}']`,
                index: 0,
                init() {
                    this.slider.on('move.after', () => {
                        this.index = this.slider.index;
                    });
                    window.fancybox.bind(this.selector);
                },
            }" x-ref="slider">
                <div class="xl:px-[100px] px-[20px]">
                    <div class="flex items-center justify-between mb-10">
                        <p class="lg:text-4xl text-xl text-slate-900 dark:text-white font-semibold">{{ $title }}</p>
                        <a href="{{ route('client.posts.index') }}"
                            class="inline-flex items-center font-medium text-blue-600 dark:text-blue-500 hover:underline" wire:navigate>
                            Смотреть все
                            <x-fas-arrow-right class="w-4 h-4 ms-2 rtl:rotate-180" />
                        </a>
                    </div>
                    <div class="glide__track mb-5" data-glide-el="track">
                        <ul class="glide__slides">
                            @foreach ($certificates as $certificate)    
                                @livewire('general.certificate', [
                                    'certificate' => $certificate,
                                    'selector' => $lightbox_selector
                                ], key($certificate->id))
                            @endforeach
                            
                        </ul>
                    </div>
                    @if (count($certificates) > 6)
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2" data-glide-el="controls[nav]">
                                @foreach ($certificates as $key=>$item)
                                    @if ($key > count($certificates) - 6)
                                        @continue
                                    @endif
                                    <div class="h-2.5 rounded-full transition-width" :class="{'w-6 bg-blue-400': index == {{ $key }}, 'w-2.5 bg-gray-400': index != {{ $key }} }" data-glide-dir="={{ $key }}"></div>
                                @endforeach
                            </div>
                            <div class="flex items-center gap-2" data-glide-el="controls">
                                <button type="button" class="text-white bg-blue-500 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm p-2 text-center inline-flex items-center dark:bg-blue-500 dark:hover:bg-blue-500 dark:focus:ring-blue-800" data-glide-dir="<">
                                    <x-fas-arrow-left-long class="w-4 h-4" />
                                    <span class="sr-only">Предыдущий слайд</span>
                                </button>
                                <button type="button" class="text-white bg-blue-500 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm p-2 text-center inline-flex items-center dark:bg-blue-500 dark:hover:bg-blue-500 dark:focus:ring-blue-800" data-glide-dir=">">
                                    <x-fas-arrow-right-long class="w-4 h-4" />
                                    <span class="sr-only">Следующий слайд</span>
                                </button>
                            </div>
                        </div>
                    @endif
                </div>
            </section>
        @else
            <div class="grid grid-cols-5 gap-4" x-data="{
                selector: `[data-fancybox='{{ $lightbox_selector }}']`,
                init() {
                    window.fancybox.bind(this.selector);
                },
            }">
                @foreach ($certificates as $certificates)    
                    @livewire('general.certificate', [
                        'certificate' => $certificates,
                        'selector' => $lightbox_selector
                    ], key($certificates->id))
                @endforeach
            </div>
        @endif
    @endif
</div>
