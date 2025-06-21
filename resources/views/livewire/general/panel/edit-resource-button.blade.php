<div class="relative" x-data="{ showTooltip: false }">
    <a 
        href="{{$link}}"
        target="_blank"
        @mouseenter="showTooltip = true"
        @mouseleave="showTooltip = false"
        class="flex justify-center items-center w-[56px] h-[56px] text-gray-500 hover:text-gray-900 bg-white rounded-full border border-gray-200 dark:border-gray-600 shadow-xs dark:hover:text-white dark:text-gray-400 hover:bg-gray-50 dark:bg-gray-700 dark:hover:bg-gray-600 focus:ring-4 focus:ring-gray-300 focus:outline-none dark:focus:ring-gray-400"
    >
        <span class="sr-only">{{ $title }}</span>
        <x-carbon-edit class="w-6 h-6" />
    </a>
    <div x-show="showTooltip"
        x-cloak
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 translate-y-1"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 translate-y-1"
        class="absolute bottom-1/2 left-full z-10 ms-4 translate-y-1/2 transform rounded-md bg-gray-900 px-3 py-2 text-sm font-medium text-white shadow-sm whitespace-nowrap">
        <span>{{ $title }}</span>
        <div class="absolute -left-1 bottom-1/2 h-2 w-2 translate-y-1/2 transform rotate-45 bg-gray-900"></div>
    </div>
</div>