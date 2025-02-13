<ul class="flex flex-col mt-4 font-medium lg:flex-row lg:space-x-8 lg:mt-0">
    @foreach ($menu->menuItems as $menuItem)
        <li>
            @if (count($menuItem->children) > 0)
                <span class="relative z-30" x-data="{ open: false }" @mouseover="open = true" @mouseleave="open = false">
                    <span
                    class="block py-2 pr-4 pl-3 text-sm text-primary-700 bg-primary-700 lg:bg-transparent lg:text-primary-700 lg:p-0 dark:text-white cursor-pointer flex items-center gap-1"
                    aria-current="page">{{ $menuItem->title }} <x-fas-arrow-turn-down class="w-2 h-2" x-show="!open" /><x-fas-arrow-turn-up class="w-2 h-2" x-show="open" /></span>
                    <ul class="absolute top-full left-0 flex flex-col font-medium p-4 bg-white border border-gray-100 dark:bg-gray-700 dark:border-gray-600 rounded gap-1" x-show="open">
                        @foreach ($menuItem->children as $child)


                        @php
                            $hasLinkable = $child->linkable ?? null;
                            $url = $hasLinkable && $child->linkable_type == 'App\Models\Page' ? Z3d0X\FilamentFabricator\Facades\FilamentFabricator::getPageUrlFromId($child->linkable->id) : $child->url;
                        @endphp

                            <li>
                                <a href="{{ $url }}" wire:navigate
                                    class="block py-2 pr-4 pl-3 text-sm text-primary-700 hover:text-blue-500 rounded bg-primary-700 lg:bg-transparent lg:text-primary-700 lg:p-0 dark:text-white dark:hover:text-blue-500 whitespace-nowrap"
                                    aria-current="page">{{ $child->title }}</a>
                            </li>
                        @endforeach
                    </ul>
                </span>
            @else
                @php
                    $hasLinkable = $menuItem->linkable ?? null;
                    $url = $hasLinkable && $menuItem->linkable_type == 'App\Models\Page' ? Z3d0X\FilamentFabricator\Facades\FilamentFabricator::getPageUrlFromId($menuItem->linkable->id) : $menuItem->url;
                @endphp
                <a href="{{ $url }}" wire:navigate
                    class="block py-2 pr-4 pl-3 text-sm text-primary-700 rounded bg-primary-700 lg:bg-transparent lg:text-primary-700 lg:p-0 dark:text-white"
                    aria-current="page">{{ $menuItem->title }}</a>
            @endif
        </li>
    @endforeach
</ul>