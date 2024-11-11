<ul class="flex flex-col mt-4 font-medium lg:flex-row lg:space-x-8 lg:mt-0">
    @foreach ($menu->menuItems as $menuItem)
        <li>
            <a href="{{ url($menuItem->url) }}" wire:navigate
                class="block py-2 pr-4 pl-3 text-sm text-primary-700 rounded bg-primary-700 lg:bg-transparent lg:text-primary-700 lg:p-0 dark:text-white"
                aria-current="page">{{ $menuItem->title }}</a>
        </li>
    @endforeach
</ul>