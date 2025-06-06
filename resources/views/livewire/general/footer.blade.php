<footer>
    <div class="bg-slate-100 border-gray-200 px-4 lg:px-6 dark:bg-slate-700">
        <div class="py-8 flex flex-wrap flex-col lg:flex-row gap-8 justify-between items-center lg:mx-auto lg:container border-b-2 border-slate-200">
            <div class="flex flex-col gap-2 lg:items-start items-center">
                <a href="/" class="flex items-center" wire:navigate>
                    <img src="{{ asset('assets/logo.svg') }}" class="mr-3 h-6 sm:h-9 block dark:hidden"
                        alt="Stella-tech Logo" />
                    <img src="{{ asset('assets/logo-dark.svg') }}" class="mr-3 h-6 sm:h-9 hidden dark:block"
                        alt="Stella-tech Logo" />
                </a>
                <a href="/" class="text-lg font-bold text-blue-500" wire:navigate>Stella-tech.ru</a>
                <span class="text-sm text-gray-400">Cкладская техника и оборудование</span>
            </div>

            @livewire('general.footer.subscription')
            @if ($menu && count($menu->menuItems) > 0)
                <div class="lg:flex items-start gap-20 hidden">
                    @foreach ($menu->menuItems as $manuItem)
                        @php
                            $hasLinkable = $manuItem->linkable ?? null;
                            $url = $hasLinkable && $manuItem->linkable_type == 'App\Models\Page' ? Z3d0X\FilamentFabricator\Facades\FilamentFabricator::getPageUrlFromId($manuItem->linkable->id) : $manuItem->url;
                        @endphp
                        <ul class="space-y-4" aria-labelledby="mega-menu-dropdown-button">
                            <li>
                                <a href="{{ $url }}" class="text-lg font-bold text-blue-500 dark:text-blue-400 hover:text-blue-600 dark:hover:text-blue-500" wire:navigate>
                                    {{ $manuItem->title }}
                                </a>
                            </li>
                            @foreach ($manuItem->children as $childMenuItem)
                                @php
                                    $hasLinkable = $childMenuItem->linkable ?? null;
                                    $url = $hasLinkable && $childMenuItem->linkable_type == 'App\Models\Page' ? Z3d0X\FilamentFabricator\Facades\FilamentFabricator::getPageUrlFromId($childMenuItem->linkable->id) : $childMenuItem->url;
                                @endphp
                                <li>
                                    <a href="{{ $url }}" class="text-sm text-gray-500 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-500" wire:navigate>
                                        {{ $childMenuItem->title }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
    <div class="bg-slate-100 border-gray-200 px-4 lg:px-6 dark:bg-slate-700 lg:block hidden">
        <div class="py-8 lg:flex gap-12 justify-between items-center mx-auto container">
            <div class="flex items-center gap-4">
                <x-fas-phone class="w-4 h-4 text-gray-900 dark:text-gray-300" />
                <div class="flex flex-col gap-2">
                    <span class="text-sm text-gray-900 dark:text-gray-300 whitespace-nowrap">+7 (495) 661-17-17</span>
                </div>
            </div>

            <div class="flex items-center gap-4">
                <x-fas-mail-bulk class="w-4 h-4 text-gray-900 dark:text-gray-300" />
                <div class="flex flex-col gap-2">
                    <span class="text-sm text-gray-900 dark:text-gray-300 whitespace-nowrap">info18@stella-technic.ru</span>
                </div>
            </div>

            <div class="flex items-center gap-4">
                <x-fas-clock-rotate-left class="w-4 h-4 text-gray-900 dark:text-gray-300" />
                <div class="flex flex-col gap-0">
                    <span class="text-sm text-gray-900 dark:text-gray-300 whitespace-nowrap">пн-пт с 9:00 до 18:00</span>
                    <span class="text-xs text-gray-700 dark:text-gray-200">отгрузки: пн-чт: с 9:30 до 17:30 пт: с 9:30 до 17:00</span>
                </div>
            </div>

            <div class="flex items-center gap-4">
                <x-fas-map-marked-alt class="w-4 h-4 text-gray-900 dark:text-gray-300" />
                <div class="flex flex-col gap-0">
                    <span class="text-sm text-gray-900 dark:text-gray-300">Московская. обл.,<br />Каширское шоссе,<br />
                        33-й километр, 9</span>
                </div>
            </div>

            <div class="flex items-center px-4 py-2.5 bg-white border border-gray-200 rounded-lg dark:bg-slate-800 dark:border-gray-700">
                <img src="{{ asset('assets/yandex.svg') }}" class="mr-3 w-24 sm:h-9 block dark:hidden" alt="">
                <img src="{{ asset('assets/yandex-dark.svg') }}" class="mr-3 w-24 sm:h-9 hidden dark:block" alt="">

                <div class="flex items-center">
                    <svg class="w-2 h-2 ms-1 text-yellow-300" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 22 20">
                        <path d="M20.924 7.625a1.523 1.523 0 0 0-1.238-1.044l-5.051-.734-2.259-4.577a1.534 1.534 0 0 0-2.752 0L7.365 5.847l-5.051.734A1.535 1.535 0 0 0 1.463 9.2l3.656 3.563-.863 5.031a1.532 1.532 0 0 0 2.226 1.616L11 17.033l4.518 2.375a1.534 1.534 0 0 0 2.226-1.617l-.863-5.03L20.537 9.2a1.523 1.523 0 0 0 .387-1.575Z"/>
                    </svg>
                    <svg class="w-2 h-2 ms-1 text-yellow-300" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 22 20">
                        <path d="M20.924 7.625a1.523 1.523 0 0 0-1.238-1.044l-5.051-.734-2.259-4.577a1.534 1.534 0 0 0-2.752 0L7.365 5.847l-5.051.734A1.535 1.535 0 0 0 1.463 9.2l3.656 3.563-.863 5.031a1.532 1.532 0 0 0 2.226 1.616L11 17.033l4.518 2.375a1.534 1.534 0 0 0 2.226-1.617l-.863-5.03L20.537 9.2a1.523 1.523 0 0 0 .387-1.575Z"/>
                    </svg>
                    <svg class="w-2 h-2 ms-1 text-yellow-300" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 22 20">
                        <path d="M20.924 7.625a1.523 1.523 0 0 0-1.238-1.044l-5.051-.734-2.259-4.577a1.534 1.534 0 0 0-2.752 0L7.365 5.847l-5.051.734A1.535 1.535 0 0 0 1.463 9.2l3.656 3.563-.863 5.031a1.532 1.532 0 0 0 2.226 1.616L11 17.033l4.518 2.375a1.534 1.534 0 0 0 2.226-1.617l-.863-5.03L20.537 9.2a1.523 1.523 0 0 0 .387-1.575Z"/>
                    </svg>
                    <svg class="w-2 h-2 ms-1 text-yellow-300" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 22 20">
                        <path d="M20.924 7.625a1.523 1.523 0 0 0-1.238-1.044l-5.051-.734-2.259-4.577a1.534 1.534 0 0 0-2.752 0L7.365 5.847l-5.051.734A1.535 1.535 0 0 0 1.463 9.2l3.656 3.563-.863 5.031a1.532 1.532 0 0 0 2.226 1.616L11 17.033l4.518 2.375a1.534 1.534 0 0 0 2.226-1.617l-.863-5.03L20.537 9.2a1.523 1.523 0 0 0 .387-1.575Z"/>
                    </svg>
                    <svg class="w-2 h-2 ms-1 text-yellow-300" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 22 20">
                        <path d="M20.924 7.625a1.523 1.523 0 0 0-1.238-1.044l-5.051-.734-2.259-4.577a1.534 1.534 0 0 0-2.752 0L7.365 5.847l-5.051.734A1.535 1.535 0 0 0 1.463 9.2l3.656 3.563-.863 5.031a1.532 1.532 0 0 0 2.226 1.616L11 17.033l4.518 2.375a1.534 1.534 0 0 0 2.226-1.617l-.863-5.03L20.537 9.2a1.523 1.523 0 0 0 .387-1.575Z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>
    <div class="bg-slate-50 border-gray-200 px-4 lg:px-6 py-2.5 dark:bg-slate-800">
        <div class="flex flex-wrap lg:flex-row flex-col justify-between items-center mx-auto container lg:text-start text-center">
            <span class="block py-2 pr-4 pl-3 text-sm text-primary-700 rounded bg-primary-700 lg:bg-transparent lg:text-primary-700 lg:p-0 dark:text-white">© 1991-2025 Все права защищены</span>
            <div class="flex lg:items-center gap-4 lg:flex-row flex-col">
                @if (setting('politics'))
                    <a href="{{ \Z3d0X\FilamentFabricator\Facades\FilamentFabricator::getPageUrlFromId(setting('politics')) }}" wire:navigate class="block py-2 pr-4 pl-3 text-sm text-primary-700 rounded bg-primary-700 lg:bg-transparent lg:text-primary-700 lg:p-0 dark:text-white" aria-current="page">Политика конфиденциальности</a>
                @endif

                @if (setting('cookies'))
                    <a href="{{ \Z3d0X\FilamentFabricator\Facades\FilamentFabricator::getPageUrlFromId(setting('cookies')) }}" wire:navigate class="block py-2 pr-4 pl-3 text-sm text-primary-700 rounded bg-primary-700 lg:bg-transparent lg:text-primary-700 lg:p-0 dark:text-white" aria-current="page">Использование cookie</a>
                @endif
                <a href="#" class="block py-2 pr-4 pl-3 text-sm text-primary-700 rounded bg-primary-700 lg:bg-transparent lg:text-primary-700 lg:p-0 dark:text-white" aria-current="page">Карта сайта</a>
            </div>
            <div class="flex mt-4 sm:justify-center sm:mt-0">
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
    <div class="lg:hidden block h-20 bg-slate-50"></div>
    @livewire('general.mobile.panel')
    @livewire('general.cookie')
</footer>
