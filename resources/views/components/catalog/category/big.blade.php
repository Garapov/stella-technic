<a href="{{ route('client.catalog', $category->urlChain()) }}" wire:navigate class="w-full bg-slate-50 dark:bg-gray-900 dark:text-gray-100 border border-blue-500 dark:border-0 relative rounded-md hover:shadow-xl cursor-pointer duration-200 p-2 flex flex-col gap-2">
  <div class="overflow-hidden rounded-md aspect-square">
    <img class="w-full h-full object-cover" alt="{{ $category->title }}" loading="lazy" class="" src="{{ Storage::disk(config('filesystems.default'))->url($category->image) }}">
  </div>
  <h5 class="text-xs font-semibold text-center">{{ $category->title }}</h5>
</a>