<header>

    <div class="bg-white px-4 lg:px-6 py-2.5 dark:bg-gray-800 shadow-sm relative z-10">
        <div class="flex flex-wrap gap-8 justify-between items-center mx-auto container">
            @if (setting('site_logo'))
                <a href="/" class="flex items-center" wire:navigate>
                    <img src="{{ Storage::disk(config('filesystems.default'))->url(setting('site_logo')) }}" class="mr-3 h-6 sm:h-9 block"
                        alt="Stella-tech Logo" />
                </a>
            @endif
            {{-- @livewire('general.header.menu') --}}
            <div class="flex gap-4 items-stretch">
                @if (setting('site_phone'))
                    <div class="flex flex-col items-end">
                        <a href="tel:88005514694" class="text-lg font-bold text-right text-slate-500 dark:text-white">{{ setting('site_phone') }}</a>
                        <div class="text-xs text-blue-600">Заказать звонок</div>
                    </div>
                    <div class="border border-slate-500"></div>
                @endif
                @if (setting('site_secondphone') && setting('site_worktime'))
                    <div class="flex flex-col">
                        <a href="tel:{{ setting('site_secondphone') }}" class="text-lg font-bold text-slate-500 dark:text-white">{{ setting('site_secondphone') }}</a>
                        <div class="text-xs text-gray-400">{{ setting('site_worktime') }}</div>
                    </div>
                @endif
                <div class="flex items-center gap-2">
                    <livewire:general.header.auth />
                    <a href="{{ route('client.favorites') }}"
                        class="relative inline-flex items-center p-3 text-sm font-medium text-center text-white bg-blue-500 rounded-lg hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 dark:bg-blue-500 dark:hover:bg-blue-500 dark:focus:ring-blue-800"
                        wire:navigate>
                        <x-fas-heart class="w-4 h-4" />
                        <div class="absolute inline-flex items-center justify-center w-6 h-6 text-xs font-bold text-white bg-red-500 border-2 border-gray-200 rounded-full -top-2 -end-2 dark:border-gray-700"
                            x-text="$store.favorites.getCount() || '0'"></div>
                    </a>

                    <a href="{{ route('client.cart') }}"
                        class="relative inline-flex items-center p-3 text-sm font-medium text-center text-white bg-blue-500 rounded-lg hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 dark:bg-blue-500 dark:hover:bg-blue-500 dark:focus:ring-blue-800"
                        wire:navigate>
                        <x-fas-cart-arrow-down class="w-4 h-4" />
                        <div class="absolute inline-flex items-center justify-center w-6 h-6 text-xs font-bold text-white bg-red-500 border-2 border-gray-200 rounded-full -top-2 -end-2 dark:border-gray-700"
                            x-text="$store.cart.cartCount()"></div>
                    </a>
                </div>
            </div>
        </div>
    </div>

</header>
