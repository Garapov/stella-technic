<div x-data="{
    isOpened: false,
    selectedCategory: {{ $categories->first()->id }},
    toggle() {
        this.isOpened = !this.isOpened;
    },
    close() {
        this.isOpened = false;
    },
    changeSelectedCategory(id) {
        this.selectedCategory = id;
    }
}" @click.outside="close">
    {!! $schema !!}
    <button type="button" class="text-white bg-blue-500 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center inline-flex items-center me-2 dark:bg-blue-500 dark:hover:bg-blue-500 dark:focus:ring-blue-800" @click="toggle">
            <x-fas-list-ul class="w-3.5 h-3.5" x-show="!isOpened" />
            <x-fas-xmark class="w-3.5 h-3.5" x-show="isOpened" x-cloak />
        <span class="hidden xl:inline ms-2">Каталог</span>
    </button>
    
    <div class="w-[calc(100vw-200px)] absolute top-full left-[50%] transform translate-x-[-50%] z-[999] hidden overflow-auto border border-blue-gray-50 bg-white p-3 font-sans text-sm font-normal text-blue-gray-500  shadow-lg shadow-blue-gray-500/10 focus:outline-none lg:block rounded-lg" role="menu" x-show="isOpened"  x-cloak>
        <div class="grid grid-cols-5 gap-3" >
            <ul class="flex flex-col max-h-[60vh] overflow-y-auto">
                @foreach ($categories->sortBy('sort') as $category)
                    <li>
                        <a href="{{ route('client.catalog', ['path' => $category->urlChain()]) }}" wire:navigate class="flex flex-center justify-between gap-2 text-gray-900 font-semibold p-2 rounded-lg" :class="{ 'text-blue-600 bg-slate-50': selectedCategory == {{ $category->id }}}" @mouseover="changeSelectedCategory({{ $category->id }})">
                            <div class="flex items-center gap-2">
                                <div class="min-w-8 max-w-8">
                                    <img src="{{ Storage::disk(config('filesystems.default'))->url($category->image) }}">
                                </div>
                                {{ $category->title }}
                            </div>
                            @if (count($category->categories))
                                <x-eva-arrow-ios-forward-outline class="min-w-4 max-w-4" x-show="selectedCategory == {{ $category->id }}" />
                            @endif
                        </a>
                    </li>
                @endforeach
            </ul>
            @foreach ($categories->sortBy('sort') as $category)
                <div class="max-h-[70vh] col-span-4"  x-show="selectedCategory == {{ $category->id }}">
                    <div class="bg-slate-50 rounded-lg h-full overflow-y-auto">
                        <ul class="grid grid-cols-3 gap-2 items-stretch p-2">
                             @php
                                $groups = $category->categories->sortBy('sort')->split(3);
                            @endphp
                            @foreach ($groups as $group)
                                <div class="flex flex-col gap-2">
                                    @foreach ($group->sortBy('sort') as $subcategory)
                                        @livewire('general.category', [
                                            'category' => $subcategory,
                                            'counts' => $variationCounts,
                                            'minPrices' => $minPrices,
                                            'allCategoryIds' => $allCategoryIds,
                                            'show_counts' => true,
                                            'show_price' => true,
                                            'show_image' => true,
                                            'transparent' => false
                                        ], key($subcategory->id))
                                    @endforeach
                                </div>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
