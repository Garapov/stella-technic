<div class="navbar-menu relative z-50">
    <div class="navbar-backdrop fixed inset-0 bg-gray-800" :class="$store.application.burger ? 'opacity-25' : 'opacity-0 pointer-events-none'" x-transition @click="$store.application.burger = false"></div>
    <nav class="fixed top-0 left-0 bottom-0 flex flex-col w-5/6 max-w-sm py-6 px-6 bg-white border-r overflow-y-auto transition-all" :class="$store.application.burger ? 'translate-x-[0]' : '-translate-x-full'">
        <div class="flex justify-between items-center mb-4">
            <div>
                @if (setting('site_logo'))
                    <a href="/" class="flex items-center" wire:navigate @click="$store.application.burger = false">
                        <img src="{{ Storage::disk(config('filesystems.default'))->url(setting('site_logo')) }}" class="mr-3 lg:h-8 h-4 block"
                            alt="Stella-tech Logo" />
                    </a>
                @endif
            </div>
            <button class="navbar-close" @click="$store.application.burger = false">
                <svg class="h-6 w-6 text-gray-400 cursor-pointer hover:text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        @if (count($categories) > 0)
            <nav>
                <ul>
                    @foreach ($categories as $category)
                        <li class="bg-white dark:bg-gray-900" x-data="{ open: false }" @click="open = !open" :class="open ? 'text-gray-900 dark:text-white': 'text-gray-500 dark:text-gray-400'">
                            <button type="button" class="flex items-center justify-between w-full py-5 font-medium text-left rtl:text-right text-gray-500 border-b border-gray-200 dark:border-gray-700 dark:text-gray-400 gap-3">
                                <span class="text-sm">{{ $category->title }}</span>
                                <span class="transition-all" :class="open ? 'rotate-180': 'rotate-0'">
                                    <x-eva-arrow-ios-downward-outline class="w-4 h-4"/>
                                </span>
                            </button>
                            @if ($category->categories)
                                <ul x-show="open" class="py-4 border-b border-gray-200 dark:border-gray-700 dark:text-gray-400">
                                    @foreach ($category->categories as $subcategory)
                                        @if ($subcategory->products->count() == 0)
                                            @continue
                                        @endif
                                        <li>
                                            <a href="{{ route('client.catalog', $subcategory->slug) }}" wire:navigate class="block py-1 text-sm text-blue-600 dark:text-blue-400" @click="$store.application.burger = false">
                                                {{ $subcategory->title }}
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        </li>
                    @endforeach
                </ul>
            </nav>
        @endif
        <div class="mt-auto">
            <div class="pt-6">
                <a class="block px-4 py-3 mb-3 leading-loose text-xs text-center font-semibold leading-none bg-gray-50 hover:bg-gray-100 rounded-xl" href="#">Sign in</a>
                <a class="block px-4 py-3 mb-2 leading-loose text-xs text-center text-white font-semibold bg-blue-600 hover:bg-blue-700  rounded-xl" href="#">Sign Up</a>
            </div>
        </div>
    </nav>
</div>