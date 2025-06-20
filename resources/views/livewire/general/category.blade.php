<div class="glide__slide p-6 bg-white border border-gray-200 rounded-lg shadow-sm dark:bg-gray-900 dark:border-gray-700 relative overflow-hidden h-auto flex flex-col justify-between gap-8">
    <div class="w-[45%] absolute top-[-10px] right-[-10px] @if($transparent) opacity-20 @endif text-blue-900 dark:opacity-20 rounded-full overflow-hidden">
        <img src="{{ Storage::disk(config('filesystems.default'))->url($category->image) }}">
    </div>
    <div class="flex flex-col items-start gap-2 @if(!$transparent) w-[65%] @endif">
        @if ($show_counts)
            <span class="bg-blue-100 text-blue-800 text-sm font-medium me-2 px-2.5 py-0.5 rounded-sm dark:bg-blue-900 dark:text-blue-300">
                @php
                    $count = $category->variationsCount();
                @endphp
                {{ $count . ' ' . ($count % 10 === 1 && $count % 100 !== 11 ? 'товар' : ($count % 10 >= 2 && $count % 10 <= 4 && ($count % 100 < 10 || $count % 100 >= 20) ? 'товара' : 'товаров')) }}
            </span>
        @endif
        <a href="{{ route('client.catalog', ['path' => $category->urlChain()]) }}" wire:navigate class="text-xl font-semibold tracking-tight text-slate-900 dark:text-white hover:text-blue-600">{{ $category->title }}</a>
        @if ($category->categories)
            <ul class="flex flex-col gap-0.5">
                @foreach ($category->categories as $subcategory)
                    @if ($subcategory->variationsCount() > 0)
                         <li>
                            <a href="{{ route('client.catalog', ['path' => $subcategory->urlChain()]) }}" wire:navigate class="text-gray-500 hover:text-gray-900 dark:hover:text-white text-xs flex items-center gap-2">
                                {{ $subcategory->title }}
                                @if ($show_counts)<span class="bg-blue-100 text-blue-800 text-sm font-medium px-1.5 py-0.5 rounded-sm dark:bg-blue-900 dark:text-blue-300 text-xs">{{ $subcategory->variationsCount() }}</span>@endif
                            </a>
                        </li>
                    @endif
                @endforeach
            </ul>
        @endif
    </div>
    @if ($category->variationsCount() && $show_price)
        <div class="text-md font-bold text-slate-600 dark:text-white">От {{ $category->minProductPrice() }} ₽</div>
    @endif
</div>
