<header>
    <div class="bg-white border-gray-200 px-4 lg:px-6 py-2.5 dark:bg-gray-800">
        <div class="flex flex-wrap justify-between items-center mx-auto container">
            <div class="hidden justify-between items-center w-full lg:flex lg:w-auto">
                @if ($topmenu && $topmenu->menuItems)
                    @livewire('general.header.topmenu', [
                        'menu' => $topmenu,
                    ])
                @endif
            </div>

            <div class="flex mt-4 sm:justify-center sm:mt-0">
                <a href="mailto:{{ setting("site_email") }}" class="flex items-center gap-2 me-10">
                    <x-fas-mail-bulk class="w-4 h-4 text-xs text-gray-800 dark:text-white" />
                    <span class="text-xs text-gray-800 dark:text-white">{{ setting("site_email") }}</span>
                </a>
                @if (setting("site_social"))
                    @foreach(setting("site_social") as $social)
                        <a href="{{ $social['link'] }}" class="text-gray-500 hover:text-gray-900 dark:hover:text-white ms-5" target="_blank">
                            <div class="w-4 h-4">
                                @svg($social['icon'])
                            </div>
                        </a>
                    @endforeach
                @endif
            </div>
        </div>
    </div>

    <div class="bg-white border-gray-200 px-4 lg:px-6 py-2.5 dark:bg-gray-800">
        <div class="flex flex-wrap gap-8 justify-between items-center mx-auto container">
            @if (setting('site_logo'))
                <a href="/" class="flex items-center" wire:navigate>
                    <img src="{{ asset('storage/' . setting('site_logo')) }}" class="mr-3 h-6 sm:h-9 block"
                        alt="Stella-tech Logo" />
                </a>
            @endif
            <div class="grow">
                <div class="flex rounded-lg border border-gray-300">
                    @livewire('general.header.popular')
                    @livewire('general.header.search')
                </div>
            </div>
            <div class="flex gap-4 items-stretch">
                @if (setting('site_phone'))
                    <div class="flex flex-col items-end">
                        <a href="tel:88005514694" class="text-lg font-bold text-right text-gray-900 dark:text-white">{{ setting('site_phone') }}</a>
                        <div class="text-xs text-blue-600">Заказать звонок</div>
                    </div>
                    <div class="border border-gray-900"></div>
                @endif
                @if (setting('site_secondphone') && setting('site_worktime'))
                    <div class="flex flex-col">
                        <a href="tel:{{ setting('site_secondphone') }}" class="text-lg font-bold text-gray-900 dark:text-white">{{ setting('site_secondphone') }}</a>
                        <div class="text-xs text-gray-400">{{ setting('site_worktime') }}</div>
                    </div>
                @endif
                @livewire('general.header.auth')
            </div>
        </div>
    </div>

    <div class="bg-gray-100 border-gray-200 px-4 lg:px-6 py-2.5 dark:bg-gray-700 relative">
        <div class="flex flex-wrap justify-between items-center mx-auto container">

            @if (count($categories) > 0)
                @livewire('general.header.catalog', [
                    'categories' => $categories,
                ])
            @endif

            @livewire('general.header.menu')
            

            <div class="flex items-center gap-2">
                <a href="{{ route('client.favorites') }}"
                    class="relative inline-flex items-center p-3 text-sm font-medium text-center text-white bg-blue-500 rounded-lg hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 dark:bg-blue-500 dark:hover:bg-blue-500 dark:focus:ring-blue-800"
                    wire:navigate>
                    <x-fas-heart class="w-4 h-4" />
                    <div class="absolute inline-flex items-center justify-center w-6 h-6 text-xs font-bold text-white bg-red-500 border-2 border-gray-200 rounded-full -top-2 -end-2 dark:border-gray-700"
                        x-text="$store.favorites.getCount() || ''" x-show="$store.favorites.getCount() > 0"></div>
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


</header>
