<div class="navbar-menu relative z-50">
    <div class="navbar-backdrop fixed inset-0 bg-gray-800 opacity-0" :class="$store.application.burger ? 'opacity-25' : 'opacity-0 pointer-events-none'" x-transition @click="$store.application.burger = false"></div>
    <div class="fixed top-0 left-0 bottom-0 flex flex-col gap-4 w-5/6 max-w-sm py-6 px-6 bg-white border-r overflow-y-auto transition-all -translate-x-full" :class="$store.application.burger ? 'translate-x-[0]' : '-translate-x-full'">
        <div class="flex justify-between items-center">
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
                            <ul x-show="open" class="py-4 border-b border-gray-200 dark:border-gray-700 dark:text-gray-400">
                                <li>
                                    <a href="{{ route('client.catalog', $category->slug) }}" wire:navigate class="flex items-center justify-between py-1 text-md font-medium text-blue-600 dark:text-blue-400" @click="$store.application.burger = false">
                                        {{ $category->title }}
                                        <span class="bg-blue-100 text-blue-800 text-xs font-medium ms-2 px-2.5 py-0.5 rounded-sm dark:bg-blue-900 dark:text-blue-300">{{ $category->products->count() }}</span>
                                    </a>
                                </li>
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
                        </li>
                    @endforeach
                </ul>
            </nav>
        @endif
        @if (setting('site_phone'))
            <div class="flex flex-col">
                <a href="tel:{{ setting('site_phone') }}" class="text-lg font-bold text-slate-700 dark:text-white">{{ setting('site_phone') }}</a>
                @if (setting('callback'))
                    <div class="text-xs text-blue-600 cursor-pointer" @click="$store.application.forms.callback = true">Заказать звонок</div>
                @endif
            </div>
            <div class="border border-slate-300"></div>
        @endif
        @if (setting('site_secondphone') && setting('site_worktime'))
            <div class="flex flex-col">
                <a href="tel:{{ setting('site_secondphone') }}" class="text-lg font-bold text-slate-700 dark:text-white">{{ setting('site_secondphone') }}</a>
                <div class="text-xs text-gray-400">{{ setting('site_worktime') }}</div>
            </div>
        @endif
    </div>
</div>