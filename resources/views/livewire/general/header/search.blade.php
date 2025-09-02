<form action="{{ route('client.search') }}" class="relative items-center grow lg:flex hidden" x-data="{
    isOpen: false,
    searchRequests: Alpine.$persist([]).as('searchRequests'),
    query: @entangle('q'),
    isLoading: false,
    init() {
        Livewire.on('queryUpdated', ({ query }) => {
            if (!this.searchRequests.includes(query)) {
                this.searchRequests.push(query);
            }
            this.isLoading = false;
        })
    },
    setQueryFromHistory(query) {
        this.isLoading = true;
        $wire.set('q', query);
    },
    findResults(event) {
        this.isLoading = true;
        $wire.set('q', event.target.value);
    },
    removeSearchQueryFromHistory(query) {
        let index = this.searchRequests.indexOf(query);
        if (index != -1) {
            this.searchRequests.splice(index, 1);
        }
    }
}" @click.outside="isOpen = false">
    <div class="rounded-lg bg-blue-500 flex items-center relative w-full z-20">
        <input type="search" id="search-dropdown"
        class="rounded-lg bg-white block p-2.5 w-full text-sm text-gray-900 dark:placeholder-gray-400 dark:text-white border border-blue-500"
        placeholder="Поиск" name="q" x-model="query" @input.debounce.500ms="findResults" @focus="isOpen = true" @input="isOpen = true" style="outline: none; box-shadow: none;" />
        <button type="submit"
            class="rounded-e-lg py-2.5 px-4 text-sm font-medium h-full text-white bg-blue-500 border border-0">
            <template x-if="!isLoading">
                <x-eva-search-outline class="w-5 h-5" />
            </template>
            <template x-if="isLoading">
                <div role="status">
                    <svg aria-hidden="true" class="w-5 h-5 text-blue-500 animate-spin dark:text-gray-600 fill-white" viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z" fill="currentColor"/>
                        <path d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z" fill="currentFill"/>
                    </svg>
                </div>
            </template>
            <span class="sr-only">Поиск</span>
        </button>
    </div>
    {{-- @if ($q != '' && $results['products']->count() > 0) --}}
        <div class="absolute top-full left-0 right-0 bg-white border border-blue-500 pt-8 pb-4 px-4 max-h-[400px] overflow-y-scroll rounded-lg -mt-4 z-10 flex flex-col gap-4"
            x-show="isOpen" x-cloak x-transition>
            @if ($q == '' && $results['products']->count() < 1)
                <span class="text-md font-semibold" x-show="JSON.parse(JSON.stringify(searchRequests)).length < 1">Вы пока ничего не искали...</span>
            @endif

            @if ($q != '' && $results['products']->count() < 1)
                <div class="flex items-center p-4 text-sm text-yellow-800 border border-yellow-300 rounded-lg bg-yellow-50 dark:bg-gray-800 dark:text-yellow-300 dark:border-yellow-800" role="alert">
                    <svg class="shrink-0 inline w-4 h-4 me-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/>
                    </svg>
                    <span class="sr-only">Info</span>
                    <div>
                        По запросу <span class="font-medium"><strong>"{{ $q }}"</strong></span> ничего не нашлось...
                    </div>
                </div>
            @endif

            <ul x-show="JSON.parse(JSON.stringify(searchRequests)).length > 0" class="flex flex-col p-4 bg-slate-50 rounded-lg">
                <li class="flex items-center justify-between mb-2">
                    <div class="text-md font-semibold">Ранее вы искали:</div>

                </li>
                <template x-for="(searchRequest, key) in JSON.parse(JSON.stringify(searchRequests)).reverse()" :key="searchRequest">
                    <li  class="flex justify-between items-center py-2 px-0 hover:shadow-md hover:px-2 hover:bg-white rounded-xl transition-all cursor-pointer" @click="setQueryFromHistory(searchRequest)" x-show="key < 5">
                        <span class="flex items-center gap-2 flex-1">
                            <span><x-carbon-time class="w-5 h-5" /></span>
                            <span x-text="searchRequest" class="text-sm text-slate-500"></span>
                        </span>

                        <div @click.stop="removeSearchQueryFromHistory(searchRequest)" class="text-red-600">
                            <x-eva-close-circle-outline class="w-6 h-6" />
                        </div>
                    </li>
                </template>
            </ul>
            @if ($results['categories']->count() > 0)
                <div class="p-4 bg-slate-50 rounded-lg">
                    <h2 class="flex items-center justify-between mb-4">
                        <div class="text-md font-semibold">Категории</div>
                        <a href="{{ route('client.search', ['q' => $q]) }}"
                            class="inline-flex items-center font-medium text-blue-600 dark:text-blue-500 hover:underline" wire:navigate>
                            Смотреть все
                            <x-fas-arrow-right class="w-4 h-4 ms-2 rtl:rotate-180" />
                        </a>
                    </h2>
                    <div class="grid grid-cols-4 xl:grid-cols-4 gap-2">
                        @foreach($results['categories'] as $key => $category)
                            @if ($key > 3)
                                @continue
                            @endif
                            <x-catalog.category.big :subcategory="$category" />
                        @endforeach
                    </div>
                </div>
            @endif
            @if ($results['products']->count() > 0)
                <div class="p-4 bg-slate-50 rounded-lg">
                    <h2 class="flex items-center justify-between mb-4">
                        <div class="text-md font-semibold">Товары</div>
                        <a href="{{ route('client.search', ['q' => $q]) }}"
                            class="inline-flex items-center font-medium text-blue-600 dark:text-blue-500 hover:underline" wire:navigate>
                            Смотреть все
                            <x-fas-arrow-right class="w-4 h-4 ms-2 rtl:rotate-180" />
                        </a>
                    </h2>
                    <div class="flex flex-col">
                        @foreach($results['products'] as $product)
                            <a href="{{ route('client.catalog', $product->urlChain()) }}" wire:navigate class="flex items-center gap-4 py-2 px-0 hover:px-2 rounded-lg hover:bg-white hover:shadow-md transition-all">
                                {{-- <img src="https://placehold.co/100x100" alt="Product" class="w-20 h-20 object-cover rounded-md"/> --}}
                                @if ($product->gallery && count($product->gallery) > 0)
                                    <img class="w-20 h-20 object-cover rounded-md" src="{{ Storage::disk(config('filesystems.default'))->url($product->gallery[0]) }}" alt="imac image" />
                                @else
                                    <img src="{{ asset('assets/placeholder.svg') }}" alt="Product1" class="w-20 h-20 object-cover rounded-md"  />
                                @endif
                                <div class="flex-1">
                                    <h3 class="font-semibold text-gray-900">{{ $product->name }} ({{ $product->sku }})</h3>
                                    <p class="text-lg text-gray-500">{{Number::format($product->price, 0)}} ₽</p>
                                </div>
                                {{-- <p class="font-semibold text-gray-900 w-20 text-right">{{Number::format($product->price, 0)}} ₽</p> --}}
                                {{-- <button class="text-gray-400 hover:text-red-500">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button> --}}
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif


        </div>
    {{-- @endif --}}
</form>
