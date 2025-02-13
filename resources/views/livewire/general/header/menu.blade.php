<div class="hidden justify-between items-center w-full lg:flex lg:w-auto">
@if ($menu && count($menu->menuItems) > 0) 
        <ul class="flex flex-col mt-4 font-medium lg:flex-row lg:space-x-8 lg:mt-0">
            @foreach ($menu->menuItems as $item)
                @php
                    $hasLinkable = $item->linkable ?? null;
                    $url = $hasLinkable && $item->linkable_type == 'App\Models\Page' ? Z3d0X\FilamentFabricator\Facades\FilamentFabricator::getPageUrlFromId($item->linkable->id) : $item->url;
                @endphp
                <li>
                    <a href="{{ $url }}"
                        class="block py-2 pr-4 pl-3 text-primary-700 rounded bg-primary-700 lg:bg-transparent lg:text-primary-700 lg:p-0 dark:text-white"
                        aria-current="page" wire:navigate>{{ $item->title }}</a>
                </li>
            @endforeach
        </ul>
    @endIf
</div>