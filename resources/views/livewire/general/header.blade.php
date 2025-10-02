<header class="shadow relative z-10">

    @livewire('general.header.burger', [
        'categories' => $categories,
        'variationCounts' => $variationCounts,
        'minPrices' => $minPrices,
        'allCategoryIds' => $allCategoryIds,
    ])

    {!! $organization !!}


    <div class="lg:bg-white bg-slate-50 border-gray-200 py-2.5 dark:bg-gray-800 relative z-30 lg:block hidden">
        <div class="flex flex-wrap justify-between items-center lg:mx-auto xl:px-[100px] px-[20px]">
            <div class="hidden justify-between items-center w-full lg:flex lg:w-auto">
                @if ($topmenu && $topmenu->menuItems)
                    @livewire('general.header.topmenu', [
                        'menu' => $topmenu,
                    ])
                @endif
            </div>

            <div class="flex mt-4 sm:justify-center items-center sm:mt-0">
                <a href="mailto:{{ setting("site_email") }}" class="flex items-center gap-2 me-10  text-blue-500">
                    <x-eva-email-outline class="w-6 h-6 text-xs dark:text-white" />
                    <span class="text-md font-bold dark:text-white">{{ setting("site_email") }}</span>
                </a>
                @if (setting("site_social"))
                    @foreach(setting("site_social") as $social)
                        <a href="{{ $social['link'] }}" class="text-gray-500 hover:text-gray-900 dark:hover:text-white ms-5" target="_blank">
                            <span class="block w-6 h-6">
                                @svg($social['icon'])
                            </span>
                        </a>
                    @endforeach
                @endif
            </div>
        </div>
    </div>

    <div class="lg:bg-white bg-slate-50 border-gray-200 py-2.5 dark:bg-gray-800 relative z-20">
        <div class="flex flex-wrap gap-8 justify-between items-center lg:mx-auto xl:px-[100px] px-[20px]">
            <button type="button" class="text-white bg-blue-500 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center inline-flex items-center dark:bg-blue-500 dark:hover:bg-blue-500 dark:focus:ring-blue-800 lg:hidden block" @click="$store.application.burger = !$store.application.burger">
                <x-fas-list-ul class="w-3.5 h-3.5"/>
            </button>
            @if (setting('site_logo'))
                <a href="/" class="flex items-center" wire:navigate>
                    <img src="{{ Storage::disk(config('filesystems.default'))->url(setting('site_logo')) }}" class="mr-3 lg:h-8 h-4 block"
                        alt="Stella-tech Logo" />
                </a>
            @endif
            @livewire('general.header.search')
                {{-- @livewire('general.header.popular') --}}
            <div class="gap-4 items-stretch lg:flex hidden">
                @if (setting('site_phone'))
                    <div class="flex flex-col items-end">
                        <a href="tel:{{ setting('site_phone') }}" class="text-lg font-bold text-right text-slate-700 dark:text-white">{{ setting('site_phone') }}</a>
                        @if (setting('callback'))
                            <div class="text-xs text-blue-600 cursor-pointer" @click="$store.application.forms.callback = true">Заказать звонок</div>
                        @endif
                    </div>
                    <div class="border border-slate-700"></div>
                @endif
                @if (setting('site_secondphone') && setting('site_worktime'))
                    <div class="flex flex-col">
                        <a href="tel:{{ setting('site_secondphone') }}" class="text-lg font-bold text-slate-700 dark:text-white">{{ setting('site_secondphone') }}</a>
                        <div class="text-xs text-gray-400">{{ setting('site_worktime') }}</div>
                    </div>
                @endif
                <livewire:general.header.auth />
            </div>
        </div>
    </div>

    <div class="lg:bg-white bg-slate-50 py-2.5 dark:bg-gray-700 relative z-10 lg:flex hidden">
        <div class="flex flex-wrap justify-between items-center lg:mx-auto xl:px-[100px] px-[20px] w-full">

            @if (count($categories) > 0)
                @livewire('general.header.catalog', [
                    'categories' => $categories,
                    'variationCounts' => $variationCounts,
                    'minPrices' => $minPrices,
                    'allCategoryIds' => $allCategoryIds,
                ])
            @endif

            @livewire('general.header.menu')


            <div class="flex items-center gap-2">
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
                    <x-fas-cart-arrow-down class="w-4 h-4 mr-2" />
                    Корзина
                    <div class="absolute inline-flex items-center justify-center w-6 h-6 text-xs font-bold text-white bg-red-500 border-2 border-gray-200 rounded-full -top-2 -end-2 dark:border-gray-700"
                        x-text="$store.cart.cartCount()"></div>
                </a>
            </div>
        </div>
    </div>


</header>
