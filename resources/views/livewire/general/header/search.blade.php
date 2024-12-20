<form action="{{ route('client.search') }}" class="relative w-full z-20" x-data="{ isOpen: false }">
    <input type="search" id="search-dropdown"
        class="block p-2.5 w-full z-20 text-sm text-gray-900 bg-gray-50 rounded-e-lg border-s-gray-50 border-s-2 border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-s-gray-700  dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:border-blue-500"
        placeholder="Поиск" name="q" wire:model.live="searchString" @focus="isOpen = true" @blur="isOpen = false" />
    <button type="submit"
        class="absolute top-0 end-0 p-2.5 text-sm font-medium h-full text-white bg-blue-700 rounded-e-lg border border-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
        <svg class="w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
        </svg>
        <span class="sr-only">Поиск</span>
    </button>
    @if ($searchString != '' && $results->count() > 0)
        <div class="absolute top-full left-0 right-0 bg-gray-50 dark:bg-gray-700 border-s-gray-50 dark:border-s-gray-700 p-4"
            x-show="isOpen">

            @forelse($results->groupByType() as $type => $modelSearchResults)
                <h2 class="text-lg font-bold mb-2">{{ $type }}</h2>

                @foreach ($modelSearchResults as $searchResult)
                    <ul>
                        <a href="{{ $searchResult->url }}" wire:navigate>{{ $searchResult->title }}</a>
                    </ul>
                @endforeach
            @empty
                <p>No results found</p>
            @endforelse
        </div>
    @endif
</form>
