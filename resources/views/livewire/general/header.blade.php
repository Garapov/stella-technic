<div>
    @if (setting("site_message"))
        <div class="py-4 text-sm text-yellow-800 rounded-lg bg-yellow-50 dark:bg-gray-800 dark:text-yellow-300" role="alert">
            <div class="xl:px-[100px] px-[20px]">
                {{ setting("site_message") }}
            </div>
        </div>
    @endif
    <header class="md:shadow relative z-50">

        @livewire('general.header.burger', [
            'categories' => $categories,
            'menu' => $topmenu,
        ])

        {!! $organization !!}
        
        <div class="lg:bg-white bg-slate-50 py-2.5 dark:bg-gray-800 relative  lg:block hidden">
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
                                    <img src="{{ Storage::disk(config('filesystems.default'))->url($social['icon']) }}" class="w-full" alt="">
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
                <div class="flex items-center gap-4">
                    <button type="button" class="items-center justify-center border border-blue-500 text-blue-500 rounded-lg w-9 h-9 lg:hidden inline-flex">
                        <x-carbon-phone class="w-3.5 h-3.5"/>
                    </button>
                    @if (setting('site_logo'))
                        <a href="/" class="flex items-center" wire:navigate>
                            <img src="{{ Storage::disk(config('filesystems.default'))->url(setting('site_logo')) }}" class="mr-3 lg:h-8 h-4 block"
                                alt="Stella-tech Logo" />
                        </a>
                    @endif
                </div>
                
                <div class="items-center grow lg:flex hidden">
                    @livewire('general.header.search')
                </div>
                    {{-- @livewire('general.header.popular') --}}
                <div class="gap-4 items-stretch lg:flex hidden">
                    @if (setting('site_phone'))
                        <div class="flex flex-col items-end">
                            <a href="tel:{{ setting('site_phone') }}" class="text-lg font-bold text-right text-slate-700 dark:text-white">{{ setting('site_phone') }}</a>
                            @if (setting('callback'))
                                <div class="text-xs text-blue-600 cursor-pointer" @click="$store.application.forms.callback = true; @if (setting('points_callback')) {{ setting('points_callback') }} @endif">Заказать звонок</div>
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
                        <x-fas-cart-arrow-down class="w-4 h-4 " />
                        <span class="hidden xl:inline ms-2">Корзина</span>
                        <div class="absolute inline-flex items-center justify-center w-6 h-6 text-xs font-bold text-white bg-red-500 border-2 border-gray-200 rounded-full -top-2 -end-2 dark:border-gray-700"
                            x-text="$store.cart.cartCount()"></div>
                    </a>
                </div>
            </div>
        </div>

        <div class="items-center grow lg:hidden flex pb-2 px-[20px] bg-slate-50">
            @livewire('general.header.search')
        </div>
    </header>

    <!-- Main modal -->
    <div class="overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
        <div class="relative p-4 w-full max-w-2xl max-h-full ">
            <!-- Modal content -->
            <div class="relative bg-neutral-primary-soft border border-default rounded-base shadow-sm p-4 md:p-6 bg-white">
                <!-- Modal header -->
                <div class="flex items-center justify-between border-b border-default pb-4 md:pb-5">
                    <h3 class="text-lg font-medium text-heading">
                        Terms of Service
                    </h3>
                    <button type="button" class="text-body bg-transparent hover:bg-neutral-tertiary hover:text-heading rounded-base text-sm w-9 h-9 ms-auto inline-flex justify-center items-center" data-modal-hide="default-modal">
                        <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18 17.94 6M18 18 6.06 6"/></svg>
                        <span class="sr-only">Close modal</span>
                    </button>
                </div>
                <!-- Modal body -->
                <div class="space-y-4 md:space-y-6 py-4 md:py-6">
                    <p class="leading-relaxed text-body">
                        With less than a month to go before the European Union enacts new consumer privacy laws for its citizens, companies around the world are updating their terms of service agreements to comply.
                    </p>
                    <p class="leading-relaxed text-body">
                        The European Union’s General Data Protection Regulation (G.D.P.R.) goes into effect on May 25 and is meant to ensure a common set of data rights in the European Union. It requires organizations to notify users as soon as possible of high-risk data breaches that could personally affect them.
                    </p>
                </div>
                <!-- Modal footer -->
                <div class="flex items-center border-t border-default space-x-4 pt-4 md:pt-5">
                    <button data-modal-hide="default-modal" type="button" class="text-white bg-brand box-border border border-transparent hover:bg-brand-strong focus:ring-4 focus:ring-brand-medium shadow-xs font-medium leading-5 rounded-base text-sm px-4 py-2.5 focus:outline-none">I accept</button>
                    <button data-modal-hide="default-modal" type="button" class="text-body bg-neutral-secondary-medium box-border border border-default-medium hover:bg-neutral-tertiary-medium hover:text-heading focus:ring-4 focus:ring-neutral-tertiary shadow-xs font-medium leading-5 rounded-base text-sm px-4 py-2.5 focus:outline-none">Decline</button>
                </div>
            </div>
        </div>
    </div>

    
</div>
