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
            <x-fas-xmark class="w-3.5 h-3.5 me-2" x-show="isOpened" />
        Каталог
    </button>
    <div class="absolute top-full left-[50%] transform translate-x-[-50%] z-[999] hidden container overflow-auto border border-blue-gray-50 bg-white p-3 font-sans text-sm font-normal text-blue-gray-500  shadow-lg shadow-blue-gray-500/10 focus:outline-none lg:block rounded-lg" role="menu" x-show="isOpened" >
        <div class="grid grid-cols-5 gap-4">
            <ul class="flex flex-col gap-4 pr-2 max-h-[60vh] overflow-y-auto">
                @foreach ($categories as $category)
                    <li>
                        <a href="{{ route('client.catalog', $category->slug) }}" wire:navigate class="flex flex-center justify-between gap-2 text-gray-600" :class="{ 'text-red-700': selectedCategory == {{ $category->id }}}" @mouseover="changeSelectedCategory({{ $category->id }})">
                            <div class="flex items-center gap-2">
                                <div class="min-w-5 max-w-5">
                                    @svg( $category->icon )
                                </div>
                                {{ $category->title }}
                            </div>
                            @if ($category->categories) 
                                <x-heroicon-o-arrow-long-right class="min-w-4 max-w-4" />
                            @endif
                        </a>
                    </li>
                @endforeach
            </ul>
            @foreach ($categories as $category)
                @if (!$category->categories) 
                    @continue
                @endif
                <div class="max-h-[60vh] col-span-4"  x-show="selectedCategory == {{ $category->id }}">
                    <div class="bg-gray-200 rounded-lg h-full overflow-y-auto">
                        <ul class="grid grid-cols-3 gap-4 items-stretch p-4">
                            @foreach ($category->categories as $subcategory)
                                @if ($subcategory->products->count() == 0)
                                    @continue
                                @endif
                                @livewire('general.category', [
                                    'category' => $subcategory,
                                ], key($subcategory->id))
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    {{-- <div class="absolute top-full left-[50%] transform translate-x-[-50%] z-[999] hidden container overflow-auto border border-blue-gray-50 bg-white p-3 font-sans text-sm font-normal text-blue-gray-500  shadow-lg shadow-blue-gray-500/10 focus:outline-none lg:block" role="menu" x-show="isOpened" >
        <ul class="grid grid-cols-3 gap-y-2 outline-none outline-0" role="menuitem">

            @foreach ($categories as $category)
                <a href="{{ route('client.catalog', $category->slug) }}" wire:navigate>
                    <button role="menuitem"
                        class="flex w-full cursor-pointer select-none items-center gap-3 rounded-lg px-3 pb-2 pt-[9px] text-start leading-tight outline-none transition-all hover:bg-blue-gray-50 hover:bg-opacity-80 hover:text-blue-gray-900 focus:bg-blue-gray-50 focus:bg-opacity-80 focus:text-blue-gray-900 active:bg-blue-gray-50 active:bg-opacity-80 active:text-blue-gray-900">
                        <div class="flex items-center justify-center rounded-lg !bg-blue-gray-50 p-2 ">
                            <div class="w-6">
                                {{ svg($category->icon) }}
                            </div>
                        </div>
                        <div>
                            <h6
                                class="flex items-center font-sans text-sm font-bold tracking-normal text-blue-gray-900 antialiased">
                                {{ $category->title }}
                            </h6>
                        </div>
                    </button>
                </a>
                @if ($category->categories)
                <ul class="ml-4">
                    @foreach ($category->categories as $subcategory)
                        <li>
                            <a href="{{ route('client.catalog', $subcategory->slug) }}" class="text-blue-gray-500 hover:underline">
                                {{ $subcategory->title }}
                            </a>
                        </li>
                    @endforeach
                </ul>
                @endif
            @endforeach

            
        </ul>
    </div> --}}
</div>