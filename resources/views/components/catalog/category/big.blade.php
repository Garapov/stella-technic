<a href="{{ route('client.catalog', $category->urlChain()) }}" wire:navigate class="w-full bg-slate-50 dark:bg-gray-900 dark:text-gray-100 border border-blue-500 dark:border-0 relative rounded-md hover:shadow-xl cursor-pointer duration-200 p-2 flex flex-col gap-2">
  <span class="bg-blue-600 text-white px-2 py-1 absolute top-0 right-0 text-xs  md:tex t-sm rounded-bl-md">
    @php
        $count = $category->variationsCount();
    @endphp
    {{ $count . ' ' . ($count % 10 === 1 && $count % 100 !== 11 ? 'товар' : ($count % 10 >= 2 && $count % 10 <= 4 && ($count % 100 < 10 || $count % 100 >= 20) ? 'товара' : 'товаров')) }}
  </span>
  <div class="overflow-hidden rounded-md aspect-square">
    <img class="w-full h-full object-cover" alt="{{ $category->title }}" loading="lazy" class="" src="{{ Storage::disk(config('filesystems.default'))->url($category->image) }}">
  </div>
  <h5 class="text-xs font-semibold text-center">{{ $category->title }}</h5>
</a>