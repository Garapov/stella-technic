<div class="glide__slide px-3 py-6 bg-white border border-gray-200 rounded-lg shadow-sm dark:bg-gray-900 dark:border-gray-700 relative overflow-hidden h-auto flex flex-col justify-between gap-8"  x-data="{
    isOpened: false
}">
    @if ($show_image)
        <div class="w-[20%] absolute top-1.5 right-0 @if($transparent) opacity-20 @endif text-blue-900 dark:opacity-20 flex items-end justify-end">
            <img src="{{ Storage::disk(config('filesystems.default'))->url($category->image) }}" class="object-cover h-full">
        </div>
    @endif
    <div class="flex flex-col items-start gap-2 @if(!$transparent) w-[80%] @endif">
        
        {{-- @if ($show_counts && isset($counts[$category->id]))
            <span class="bg-blue-100 text-blue-800 text-sm font-medium me-2 px-2.5 py-0.5 rounded-sm dark:bg-blue-900 dark:text-blue-300">
                @php
                    $count = $counts[$category->id] ?? 0;
                @endphp
                {{ $count . ' ' . ($count % 10 === 1 && $count % 100 !== 11 ? 'товар' : ($count % 10 >= 2 && $count % 10 <= 4 && ($count % 100 < 10 || $count % 100 >= 20) ? 'товара' : 'товаров')) }}
            </span>
        @endif --}}
        <div class="flex gap-2 items-start">
            @if (count($category->categories))
                <div class="min-w-5 min-h-6 max-w-5 max-h-5 pt-1 cursor-pointer" @click="isOpened = !isOpened">
                    <x-heroicon-o-plus-circle class="w-full h-full" x-show="!isOpened" />
                    <x-heroicon-o-minus-circle class="w-full h-full" x-show="isOpened" />
                </div>
            @endif
            <a href="{{ route('client.catalog', ['path' => $category->urlChain()]) }}" wire:navigate class="text-lg font-semibold tracking-tight text-slate-900 dark:text-white hover:text-blue-600">
            
                {{ $category->title }}
            </a>
        </div>
        
        {{-- @if (count($category->categories)) --}}
            <ul class="flex flex-col gap-0.5" x-show="isOpened">
                @foreach ($category->categories->sortBy('sort') as $subcategory)
                    <li>
                    <a href="{{ route('client.catalog', ['path' => $subcategory->urlChain()]) }}" wire:navigate class="text-slate-900 dark:hover:text-white text-sm flex items-center gap-2 p-1.5 hover:bg-gray-100 rounded-lg">
                        {{ $subcategory->title }}
                        {{-- @if ($show_counts && isset($counts[$subcategory->id]))<span class="bg-blue-100 text-blue-800 text-sm font-medium px-1.5 py-0.5 rounded-sm dark:bg-blue-900 dark:text-blue-300 text-xs">{{ $counts[$subcategory->id] }}</span>@endif --}}
                    </a>
                </li>
                @endforeach
            </ul>
        {{-- @endif --}}
    </div>
    {{-- @if (isset($minPrices[$category->id]) && $show_price)
        <div class="text-md font-bold text-slate-600 dark:text-white">От {{ $minPrices[$category->id] }} ₽</div>
    @endif --}}
</div>
