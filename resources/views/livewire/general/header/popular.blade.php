<div>
    @if ($menu && count($menu->menuItems) > 0) 
        <div class="relative" x-data="dropdown" @click.outside="close">
            <button class="flex-shrink-0 z-10 inline-flex items-center py-2.5 px-4 text-sm font-medium text-center text-gray-900 bg-gray-100 border border-gray-300 rounded-s-lg hover:bg-gray-200 focus:ring-4 focus:outline-none focus:ring-gray-100 dark:bg-gray-700 dark:hover:bg-gray-600 dark:focus:ring-gray-700 dark:text-white dark:border-gray-600 whitespace-nowrap" type="button" @click="toggle">
                {{ $menu->name }}
                <x-fas-arrow-down class="w-2.5 h-2.5 ms-2.5" x-show="!isOpened"/>
                <x-fas-arrow-up class="w-2.5 h-2.5 ms-2.5" x-show="isOpened"/>                            
            </button>
            <div class="absolute top-[calc(100%+10px)] w-full z-10 bg-white divide-y divide-gray-100 rounded-lg shadow w-44 dark:bg-gray-700" x-show="isOpened">
                <ul class="py-2 text-sm text-gray-700 dark:text-gray-200" aria-labelledby="dropdown-button">
                    @foreach ($menu->menuItems as $item)

                        @php
                            $hasLinkable = $item->linkable ?? null;
                            $url = $hasLinkable && $item->linkable_type == 'App\Models\Page' ? Z3d0X\FilamentFabricator\Facades\FilamentFabricator::getPageUrlFromId($item->linkable->id) : $item->url;
                        @endphp
                        <li>
                            <a href="{{ $url }}"
                                class="inline-flex w-full px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white" wire:navigate>{{ $item->title }}</a>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif
</div>