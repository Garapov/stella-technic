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
    <button type="button" class="text-white bg-blue-500 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center inline-flex items-center me-2 dark:bg-blue-500 dark:hover:bg-blue-500 dark:focus:ring-blue-800" @click="toggle">
            <x-fas-list-ul class="w-3.5 h-3.5 me-2" x-show="!isOpened" />
            <x-fas-xmark class="w-3.5 h-3.5 me-2" x-show="isOpened" x-cloak />
        Каталог
    </button>
    <div class="absolute top-full left-[50%] transform translate-x-[-50%] z-[999] hidden container overflow-auto border border-blue-gray-50 bg-white p-3 font-sans text-sm font-normal text-blue-gray-500  shadow-lg shadow-blue-gray-500/10 focus:outline-none lg:block rounded-lg" role="menu" x-show="isOpened"  x-cloak>
        <div class="grid grid-cols-5 gap-4" >
            <ul class="flex flex-col gap-4 pr-2 max-h-[60vh] overflow-y-auto">
                @foreach ($categories as $category)
                    <li>
                        <a href="{{ route('client.catalog', $category->slug) }}" wire:navigate class="flex flex-center justify-between gap-2 text-gray-600" :class="{ 'text-red-700': selectedCategory == {{ $category->id }}}" @mouseover="changeSelectedCategory({{ $category->id }})">
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
            @foreach ($categories as $category)
                @if (!$category->categories)
                    @continue
                @endif
                <div class="max-h-[70vh] col-span-4"  x-show="selectedCategory == {{ $category->id }}">
                    <div class="bg-gray-200 rounded-lg h-full overflow-y-auto">
                        <ul class="grid grid-cols-3 gap-4 items-stretch p-4">
                            @foreach ($category->categories as $subcategory)
                                @if ($subcategory->products->count() == 0)
                                    @continue
                                @endif
                                @livewire('general.category', [
                                    'category' => $subcategory,
                                    'show_counts' => true,
                                    'show_price' => true,
                                    'transparent' => true
                                ], key($subcategory->id))
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
