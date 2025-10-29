<div>
    @if ($schema)
        {!! $schema !!}
    @endif
    <ul class="flex items-center gap-4 mt-4 font-medium lg:mt-0">
        @foreach ($menu->menuItems as $menuItem)
            <li>
                @if (count($menuItem->children) > 0)
                    <span class="relative z-30" x-data="{ open: false }" @mouseover="open = true" @mouseleave="open = false" x-cloak>
                        <span
                        class="block py-2 pr-4 pl-3 text-sm text-slate-500 hover:text-blue-500 lg:p-0 dark:text-white cursor-pointer flex items-center gap-1"
                        aria-current="page">{{ $menuItem->title }} <x-eva-arrow-ios-downward class="w-3 h-3" x-show="!open" /><x-eva-arrow-ios-upward class="w-3 h-3" x-show="open" /></span>
                        <ul class="absolute top-full left-0 flex flex-col font-medium p-4 bg-white border border-blue-500 dark:bg-gray-700 dark:border-gray-600 rounded gap-1 z-60" x-show="open">
                            @foreach ($menuItem->children as $child)


                            @php
                                $hasLinkable = $child->linkable ?? null;
                                $url = $hasLinkable && $child->linkable_type == 'App\Models\Page' ? Cache::rememberForever('fabricator:page_' . $child->linkable->id . '_url', function () use ($child) { return Z3d0X\FilamentFabricator\Facades\FilamentFabricator::getPageUrlFromId($child->linkable->id); }) : $child->url;
                            @endphp

                                <li>
                                    <a href="{{ $url }}" wire:navigate
                                        class="block py-2 pr-4 pl-3 text-sm rounded lg:p-0 text-slate-500 hover:text-blue-500 whitespace-nowrap"
                                        aria-current="page">{{ $child->title }}</a>
                                </li>
                            @endforeach
                        </ul>
                    </span>
                @else
                    @php
                        $hasLinkable = $menuItem->linkable ?? null;
                        $url = $hasLinkable && $menuItem->linkable_type == 'App\Models\Page' ? Cache::rememberForever('fabricator:page_'.$menuItem->linkable->id.'_url', function () use ($menuItem) { return Z3d0X\FilamentFabricator\Facades\FilamentFabricator::getPageUrlFromId($menuItem->linkable->id); }) : $menuItem->url;
                    @endphp
                    <a href="{{ $url }}" wire:navigate
                        class="block py-2 pr-4 pl-3 text-sm text-primary-700 rounded bg-primary-700 lg:bg-transparent lg:text-primary-700 lg:p-0 dark:text-white"
                        aria-current="page">{{ $menuItem->title }}</a>
                @endif
            </li>
            
        @endforeach
    </ul>
</div>