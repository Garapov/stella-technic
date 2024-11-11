<div x-data="dropdown" @click.outside="close">
    <button type="button" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center inline-flex items-center me-2 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800" @click="toggle">
            <x-fas-list-ul class="w-3.5 h-3.5 me-2" x-show="!isOpened" />
            <x-fas-xmark class="w-3.5 h-3.5 me-2" x-show="isOpened" />
        Каталог
    </button>
    <div class="absolute top-full left-[50%] transform translate-x-[-50%] z-[999] hidden container overflow-auto border border-blue-gray-50 bg-white p-3 font-sans text-sm font-normal text-blue-gray-500  shadow-lg shadow-blue-gray-500/10 focus:outline-none lg:block" role="menu" x-show="isOpened" >
        <ul class="grid grid-cols-3 gap-y-2 outline-none outline-0" role="menuitem">

            @foreach ($categories as $category)
                <a href="{{ route('client.catalog', $category->slug) }}" wire:navigate>
                    <button role="menuitem"
                        class="flex w-full cursor-pointer select-none items-center gap-3 rounded-lg px-3 pb-2 pt-[9px] text-start leading-tight outline-none transition-all hover:bg-blue-gray-50 hover:bg-opacity-80 hover:text-blue-gray-900 focus:bg-blue-gray-50 focus:bg-opacity-80 focus:text-blue-gray-900 active:bg-blue-gray-50 active:bg-opacity-80 active:text-blue-gray-900">
                        <div class="flex items-center justify-center rounded-lg !bg-blue-gray-50 p-2 ">
                            <img src="{{ asset('storage/'. $category->image) }}" class="w-6" alt="">
                        </div>
                        <div>
                            <h6
                                class="flex items-center font-sans text-sm font-bold tracking-normal text-blue-gray-900 antialiased">
                                {{ $category->name }}
                            </h6>
                            {{-- <p class="block font-sans text-xs !font-medium text-blue-gray-500 antialiased">
                                Find the perfect solution for your needs.
                            </p> --}}
                        </div>
                    </button>
                </a>
            @endforeach

            
        </ul>
    </div>
</div>