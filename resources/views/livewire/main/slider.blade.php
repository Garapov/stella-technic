<div>
    @if (count($slides) > 0)
        <div class="py-24  bg-gray-200 dark:bg-gray-700">
            <div class="m-auto container">
                <div class="glide" id="main-slider" x-data="main_slider">
                    <div class="glide__track" data-glide-el="track">
                        <div class="glide__slides">
                            @foreach ($slides as $slide)
                                <div class="h-[300px] whitespace-normal">
                                    <div class="grid grid-cols-2 gap-10">
                                        <div class="flex flex-col gap-4 items-start justify-between">
                                            <div>
                                                <div class="text-3xl mb-4 uppercase text-grey-700 dark:text-white">{{ $slide->title }}</div>
                                                <div class="text-smtext-grey-700 dark:text-white">{{ $slide->description }}</div>
                                            </div>

                                            <a href="{{ url($slide->link) }}" class="text-white bg-gradient-to-br from-purple-600 to-blue-500 hover:bg-gradient-to-bl focus:ring-4 focus:outline-none focus:ring-blue-300 dark:focus:ring-blue-800 font-medium rounded-lg text-sm px-5 py-2.5 text-center me-2 mb-2" wire:navigate>{{ $slide->button_text }}</a>
                                        </div>
                                        <div class="flex items-center justify-center h-[300px]">
                                            <img class="h-full rounded-lg" src="{{ asset($slide->image) }}" alt="">
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="flex items-center justify-between mt-4">
                        <div class="flex items-center gap-2" data-glide-el="controls[nav]">
                            @foreach ($slides as $key=>$slide)
                                <div class="h-2.5 rounded-full transition-width" :class="{'w-6 bg-blue-400': index == {{ $key }}, 'w-2.5 bg-gray-400': index != {{ $key }} }" data-glide-dir="={{ $key }}"></div>
                            @endforeach
                        </div>
                        <div class="flex items-center gap-2" data-glide-el="controls">
                            <button type="button" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm p-2.5 text-center inline-flex items-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800" data-glide-dir="<">
                                <x-fas-arrow-left-long class="w-5 h-5" />
                                <span class="sr-only">Icon description</span>
                            </button>
                            <button type="button" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm p-2.5 text-center inline-flex items-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800" data-glide-dir=">">
                                <x-fas-arrow-right-long class="w-5 h-5" />
                                <span class="sr-only">Icon description</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

