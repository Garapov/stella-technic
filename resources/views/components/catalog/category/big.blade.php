<a href="{{ route('client.catalog', $subcategory->urlChain()) }}" class="p-2.5 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-slate-200 shadow-sm hover:shadow-lg shadow-slate-50/50 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700 flex flex-col-reverse md:flex-row items-center justify-between gap-2">
    <span>{{ $subcategory->title }}</span>
    @if (!empty($subcategory->image))
        <img class="md:min-w-20 md:max-w-20 md:min-h-20 md:max-h-20 w-[80%] md:mx-0 mx-auto object-cover" alt="{{ $subcategory->title }}" loading="lazy" class="" src="{{ Storage::disk(config('filesystems.default'))->url($subcategory->image) }}">
    @endif
</a>