<div>
    @if (count($slides) > 0)
        <div class="lg:py-10  bg-white dark:bg-gray-700" x-data="{
            slider: new window.glide($refs.slider, {
                autoplay: 5000,
                gap: 0,
            }).mount(),
            index: 0,
            init() {
                this.slider.on('move.after', () => {
                    this.index = this.slider.index;
                })
            },
        }">
            <div class="xl:px-[100px] px-[20px]">
                <div class="glide relative group" x-ref="slider">
                    <div class="glide__track lg:rounded-xl overflow-hidden" data-glide-el="track">
                        <div class="glide__slides">
                            @foreach ($slides as $slide)
                                <div class="whitespace-normal @if (!$slide->background_image) p-10 @endif" style="background-color: {{ $slide->background }};">
                                    @if ($slide->background_image)
                                        <a href="{{ url($slide->link) }}" wire:navigate>
                                            <img class="w-full object-cover object-center aspect-[31/10] text-0" src="{{ Storage::disk(config('filesystems.default'))->url($slide->background_image) }}" alt="">
                                        </a>
                                    @else
                                        <div class="grid grid-cols-2 gap-10 h-full">
                                            <div class="flex flex-col gap-4 items-start justify-between">
                                                <div>
                                                    <div class="text-3xl mb-4 uppercase text-grey-700 dark:text-white">{{ $slide->title }}</div>
                                                    <div class="text-sm text-grey-700 dark:text-white">{!!html_entity_decode($slide->description)!!}</div>
                                                </div>

                                                <a href="{{ url($slide->link) }}" class="text-white bg-gradient-to-br from-purple-600 to-blue-500 hover:bg-gradient-to-bl focus:ring-4 focus:outline-none focus:ring-blue-300 dark:focus:ring-blue-800 font-medium rounded-lg text-sm px-5 py-2.5 text-center" wire:navigate>{{ $slide->button_text }}</a>
                                            </div>
                                            <div class="flex items-center justify-center h-full">
                                                @if ($slide->image)
                                                    <img class="rounded-lg" src="{{ Storage::disk(config('filesystems.default'))->url($slide->image) }}" alt="">
                                                @endif
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                    @if (count($slides) > 1)
                        <div class="group-hover:opacity-70 opacity-0 flex items-center justify-between gap-4 p-2 absolute right-0 bottom-0 bg-white dark:bg-gray-700 rounded-tl-lg">
                            <div class="flex items-center gap-2" data-glide-el="controls[nav]">
                                @foreach ($slides as $key=>$slide)
                                    <div class="h-2.5 rounded-full transition-width" :class="{'w-6 bg-blue-400': index == {{ $key }}, 'w-2.5 bg-gray-400': index != {{ $key }} }" data-glide-dir="={{ $key }}"></div>
                                @endforeach
                            </div>
                            <div class="flex items-center gap-2" data-glide-el="controls">
                                <button type="button" class="text-white bg-blue-500 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm p-2 text-center inline-flex items-center dark:bg-blue-500 dark:hover:bg-blue-500 dark:focus:ring-blue-800" data-glide-dir="<">
                                    <x-eva-arrow-ios-back class="w-4 h-4" />
                                    <span class="sr-only">Предыдущий слайд</span>
                                </button>
                                <button type="button" class="text-white bg-blue-500 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm p-2 text-center inline-flex items-center dark:bg-blue-500 dark:hover:bg-blue-500 dark:focus:ring-blue-800" data-glide-dir=">">
                                    <x-eva-arrow-ios-forward class="w-4 h-4" />
                                    <span class="sr-only">Следующий слайд</span>
                                </button>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>
