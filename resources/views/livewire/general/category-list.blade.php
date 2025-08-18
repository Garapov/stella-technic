

<div class="flex flex-col gap-2" x-data="{
    isOpened: false
}">
    <div class="flex gap-2 items-start text-gray-600">

        <div class="w-5 h-5 cursor-pointer @if (!count($category->categories)) opacity-0 @endif" @click="isOpened = !isOpened">
            <x-heroicon-o-plus-circle class="w-full h-full" x-show="!isOpened" />
            <x-heroicon-o-minus-circle class="w-full h-full" x-show="isOpened" />
        </div>
        
        <a href="{{ route('client.catalog', ['path' => $category->urlChain()]) }}" wire:navigate class="text-md font-semibold">{{ $category->title }}</a>
    </div>

    @if (count($category->categories))
        <div class="flex gap-2 items-start text-gray-600" x-show="isOpened" >

            <div class="w-5 h-5"></div>
            
            <ul class="flex flex-col gap-0.5">
                @foreach ($category->categories->sortBy('sort') as $subcategory)
                    <li>
                    <a href="{{ route('client.catalog', ['path' => $subcategory->urlChain()]) }}" wire:navigate class="text-gray-500 hover:text-gray-900 dark:hover:text-white text-xs flex items-center gap-2">
                        {{ $subcategory->title }}
                        {{-- @if ($show_counts && isset($counts[$subcategory->id]))<span class="bg-blue-100 text-blue-800 text-sm font-medium px-1.5 py-0.5 rounded-sm dark:bg-blue-900 dark:text-blue-300 text-xs">{{ $counts[$subcategory->id] }}</span>@endif --}}
                    </a>
                </li>
                @endforeach
            </ul>
        </div>
    @endif
</div>