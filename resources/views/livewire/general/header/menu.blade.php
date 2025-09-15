<div class="hidden justify-between items-center w-full lg:flex lg:w-auto">
    @if ($menu && count($menu->menuItems) > 0)
        {!! $schema !!}
        <ul class="flex items-center gap-4 mt-4 font-medium lg:mt-0">
            @foreach ($menu->menuItems as $item)
                @php
                    $hasLinkable = $item->linkable ?? null;
                    $url = $hasLinkable && $item->linkable_type == 'App\Models\Page' ? Z3d0X\FilamentFabricator\Facades\FilamentFabricator::getPageUrlFromId($item->linkable->id) : $item->url;
                @endphp
                <li>
                    <a href="{{ $url }}"
                        class="block py-2 pr-4 pl-3 text-slate-900 rounded lg:p-0 dark:text-white hover:text-blue-500 text-sm"
                        aria-current="page" wire:navigate>{{ $item->title }}</a>
                </li>
                @if (!$loop->last)
                    <li class="text-slate-900 text-sm">|</li>
                @endif
            @endforeach
        </ul>
    @endIf
</div>