<a href="{{ route('client.catalog', $subcategory->urlChain()) }}" wire:navigate class="p-2.5 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-slate-200 shadow-sm hover:shadow-lg shadow-slate-50/50 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 flex items-center justify-between gap-2">
  {{-- {{ $subcategory->urlChain() }} --}}
  <h5 class="text-md font-semibold text-center">{{ $subcategory->title }}</h5>
  @if (!empty($subcategory->image))
    <img class="min-w-10 max-w-10 min-h-10 max-h-10 object-cover" alt="{{ $subcategory->title }}" loading="lazy" class="" src="{{ Storage::disk(config('filesystems.default'))->url($subcategory->image) }}">
  @endif
  
</a>