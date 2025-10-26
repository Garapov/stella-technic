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
                            perView: 2,
                            peek: {
                            before: 0,
                                after: 100
                            },
                        },
                        560: {
                            perView: 1,
                            peek: {
                                before: 0,
                                after: 100
                            },
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
                            class=" items-center font-medium text-blue-600 dark:text-blue-500 hover:underline hidden md:inline-flex" wire:navigate>
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
                    <a href="{{ route('client.posts.index') }}"
                            class="inline-flex items-center font-medium text-blue-600 dark:text-blue-500 hover:underline md:hidden" wire:navigate>
                            Смотреть все
                            <x-fas-arrow-right class="w-4 h-4 ms-2 rtl:rotate-180" />
                        </a>
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
