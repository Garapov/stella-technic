<a href="{{ route('client.catalog', $subcategory->urlChain()) }}" wire:navigate class="w-full bg-slate-50 dark:bg-gray-900 dark:text-gray-100 border border-blue-500 dark:border-0 relative rounded-md hover:shadow-xl cursor-pointer duration-200 p-2 flex flex-col gap-2">
  {{-- {{ $subcategory->urlChain() }} --}}
  <div class="overflow-hidden rounded-md aspect-square">

    @if (!empty($subcategory->image))
      <img class="w-full h-full object-cover" alt="{{ $subcategory->title }}" loading="lazy" class="" src="{{ Storage::disk(config('filesystems.default'))->url($subcategory->image) }}">
    @endif
  </div>
  <h5 class="text-xs font-semibold text-center">{{ $subcategory->title }}</h5>
</a>