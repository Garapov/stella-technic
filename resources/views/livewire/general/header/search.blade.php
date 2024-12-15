<div class="relative w-full z-20">
    <input type="search" id="search-dropdown"
        class="block p-2.5 w-full z-20 text-sm text-gray-900 bg-gray-50 rounded-e-lg border-s-gray-50 border-s-2 border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-s-gray-700  dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:border-blue-500"
        placeholder="Поиск" wire:model.live="searchString" />
    <button type="submit"
        class="absolute top-0 end-0 p-2.5 text-sm font-medium h-full text-white bg-blue-700 rounded-e-lg border border-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
        <svg class="w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
            viewBox="0 0 20 20">
            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
        </svg>
        <span class="sr-only">Поиск</span>
    </button>
    <div class="absolute top-full left-0 right-0">
        There are {{ $results->count() }} results.

        @foreach($results->groupByType() as $type => $modelSearchResults)
        <h2>{{ $type }}</h2>
        
            @foreach($modelSearchResults as $searchResult)
                <ul>
                        <a href="{{ $searchResult->url }}">{{ $searchResult->title }}</a>
                </ul>
            @endforeach
        @endforeach
    </div>
</div>