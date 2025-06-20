
<section class="py-8 bg-white md:py-10 dark:bg-gray-900 antialiased">
    @if ($category || $product_ids)
    <div class="mx-auto container relative">

            <!-- Loading Overlay -->
            <div wire:loading.class.remove="hidden"
                class="fixed inset-0 z-[9999] flex items-center justify-center bg-black/20 backdrop-blur-sm hidden">
                <div class="flex items-center gap-2 rounded-lg bg-white/80 px-6 py-4 shadow-lg dark:bg-gray-800/80">
                    <div class="animate-spin w-6 h-6 border-4 border-blue-600 border-t-transparent rounded-full"></div>
                    <span class="text-gray-700 dark:text-gray-300">Загрузка...</span>
                </div>
            </div>
            @php
                $all_products = $products->get();
                $paginated_products = $products->paginate(40);
            @endphp
            <!-- Heading & Filters -->
            @if ($category)
                @if ($category->seo)
                    @forelse($category->seo as $seo_tag)
                        @foreach($seo_tag['data'] as $key => $tag)

                            @if ($key == 'image')
                                @seo(['image' => Storage::disk(config('filesystems.default'))->url($tag)])
                            @else
                                @seo([$key => $tag])
                            @endif
                        @endforeach

                    @empty
                        @seo(['title' => $category->title])
                        @seo(['description' => $category->description])
                        @seo(['image' => Storage::disk(config('filesystems.default'))->url($category->image)])
                    @endforelse
                @else
                    @seo(['title' => $category->title])
                    @seo(['description' => $category->description])
                    @seo(['image' => Storage::disk(config('filesystems.default'))->url($category->image)])
                @endif
                <div class="mb-5">
                    @livewire('general.breadcrumbs')
                </div>
                @if ($category->categories)
                    <div class="grid grid-cols-8 gap-4 mb-4">
                        @foreach ($category->categories->where('is_tag', false) as $subcategory)
                            @if ($subcategory->products->count() == 0)
                                @continue
                            @endif
                            <x-catalog.category.big :category="$subcategory" />
                        @endforeach
                    </div>
                    <ul class="flex items-center gap-2 overflow-auto pb-2 mb-4">
                        @foreach ($category->categories->where('is_tag', true) as $subcategory)
                            @if ($subcategory->products->count() == 0)
                                @continue
                            @endif
                            <li>
                                <x-catalog.category.small :category="$subcategory" />
                            </li>
                        @endforeach
                    </ul>
                @endif
            @endif





            <div class="grid grid-cols-9 gap-4">
                @if ($display_filter)
                    <div class="col-span-2">
                        @livewire('catalog.filter', [
                            'products' => $all_products,
                        ])

                    </div>
                @endif
                <div class="flex flex-col gap-4 @if ($display_filter) col-span-7 @else col-span-full @endif">
                    @if ($category && $category->title)
                        <div class="items-end justify-between flex">
                            <div>

                                <h1 class="text-xl font-semibold text-gray-900 dark:text-white sm:text-2xl flex items-center">

                                        <span>{{ $category->title }}</span> <span class="bg-blue-100 text-blue-800 text-xs font-medium ms-2 px-2.5 py-0.5 rounded-sm dark:bg-blue-900 dark:text-blue-300">
                                            @php
                                                $count = $all_products->count();
                                            @endphp
                                            {{ $count . ' ' . ($count % 10 === 1 && $count % 100 !== 11 ? 'товар' : ($count % 10 >= 2 && $count % 10 <= 4 && ($count % 100 < 10 || $count % 100 >= 20) ? 'товара' : 'товаров')) }}
                                        </span>

                                </h1>
                            </div>

                            <div class="flex items-center space-x-4">
                                <!-- Display Mode Toggle -->
                                <div class="inline-flex rounded-md shadow-xs" role="group">
                                    <div class="inline-flex items-center px-3 py-2 text-sm font-medium border rounded-s-lg @if ($mode === 'list') bg-blue-500 border-blue-500 text-white @else bg-white border-gray-200 text-gray-900 cursor-pointer @endif"
                                        wire:click="changeDisplayMode('list')">
                                        <x-carbon-horizontal-view class="w-4 h-4" />
                                    </div>
                                    <div class="inline-flex items-center px-3 py-2 text-sm font-medium border rounded-e-lg @if ($mode === 'block') bg-blue-500 border-blue-500 text-white @else bg-white border-gray-200 text-gray-900 cursor-pointer @endif"
                                        wire:click="changeDisplayMode('block')" >
                                        <x-carbon-vertical-view class="w-4 h-4" />
                                    </div>
                                </div>

                                <!-- Sort Dropdown -->
                                <div class="relative" x-data="{sortingOpened: false}">
                                    <button type="button" class="flex w-full items-center justify-center rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm font-medium text-gray-900 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:outline-none focus:ring-4 focus:ring-gray-100 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white dark:focus:ring-gray-700 sm:w-auto"
                                        @click="sortingOpened = true;">
                                        <svg class="-ms-0.5 me-2 h-4 w-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $this->getSortOptions()[$sort]['icon'] }}" />
                                        </svg>
                                        {{ $this->getSortOptions()[$sort]['label'] }}
                                    </button>

                                    <!-- Sort Options Dropdown -->
                                    <div class="absolute right-0 top-[calc(100%+10px)] z-50 w-48 divide-y divide-gray-100 rounded-lg bg-white shadow dark:bg-gray-700"
                                        @click.outside="sortingOpened = false"
                                        x-show="sortingOpened"
                                        style="display: none;">
                                        <ul class="p-2 text-left text-sm font-medium text-gray-500 dark:text-gray-400">
                                            @foreach ($this->getSortOptions() as $value => $option)
                                                <li>
                                                    <button type="button" @click="$wire.updateSort('{{ $value }}'); sortingOpened = false"
                                                        @class([
                                                            'inline-flex w-full items-center px-2 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white rounded-lg',
                                                            'text-blue-600 dark:text-blue-500' => $sort === $value,
                                                            'text-gray-500 dark:text-gray-400' => $sort !== $value,
                                                        ])>
                                                        <svg class="-ms-0.5 me-2 h-4 w-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $option['icon'] }}" />
                                                        </svg>
                                                        {{ $option['label'] }}
                                                    </button>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                    @if($paginated_products->isEmpty())
                        <div class="flex flex-col items-center justify-center p-8 text-center">
                            <div class="mb-4">
                                <svg class="w-12 h-12 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <h3 class="mb-2 text-xl font-semibold text-gray-900 dark:text-white">Ничего не найдено</h3>
                            <p class="text-gray-500 dark:text-gray-400">По выбранным фильтрам товары не найдены. Попробуйте изменить параметры поиска.</p>
                            <button wire:click="resetFilters" class="mt-4 inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:ring-4 focus:outline-none focus:ring-blue-300 dark:bg-blue-500 dark:hover:bg-blue-600 dark:focus:ring-blue-800">
                                <svg class="w-4 h-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                </svg>
                                Сбросить фильтры
                            </button>
                        </div>
                    @else
                        <div>
                            @if ($displayMode == 'block')
                                <div>
                                    <div class="mb-4 grid gap-4 sm:grid-cols-1 md:mb-8 @if ($display_filter) lg:grid-cols-2 xl:grid-cols-4 @else lg:grid-cols-3 xl:grid-cols-5 @endif">
                                        @foreach ($paginated_products as $variant)
                                            @livewire('general.product-variant', [
                                                'variant' => $variant,
                                                'category' => $category ?? null,
                                            ], key('variant_' . $variant->id))
                                        @endforeach

                                    </div>
                                    {{ $paginated_products->links() }}
                                </div>
                            @else
                                @php
                                    $batches = $all_products->where('batch_id', '!=', null)->groupBy('batch_id');
                                @endphp
                                <div class="flex flex-col gap-4">
                                    @forelse ($batches as $key => $batch)
                                        <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-900 relative flex flex-col gap-4">
                                            <h3 class="text-lg sm:text-xl font-semibold text-slate-900 text-grey-600 dark:text-white">{{ $batch->first()->batch->name }}</h3>
                                            <div class="grid grid-cols-4 gap-4">
                                                <div class="rounded-lg overflow-hidden">
                                                    <img src="{{ Storage::disk(config('filesystems.default'))->url($batch->first()->batch->image) }}" alt="" />
                                                </div>
                                                <div class="col-span-3 flex flex-col gap-4">
                                                    <div class="flex flex-col gap-2 text-gray-600 dark:text-gray-400 p-4 rounded-xl bg-gray-100 dark:bg-gray-600">
                                                        {!! str($batch->first()->batch->description)->sanitizeHtml() !!}
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="relative overflow-x-auto">
                                                @php
                                                    // Сбор всех параметров с show_on_table == true
                                                    $uniqueParamNames = collect();
                                                    foreach ($batch as $item) {
                                                        // Сбор параметров из paramItems
                                                        if ($item->paramItems) {
                                                            foreach ($item->paramItems as $paramItem) {
                                                                if ($paramItem->productParam && $paramItem->productParam->show_on_table) {
                                                                    $uniqueParamNames[$paramItem->productParam->name] = [
                                                                        'icon' => $paramItem->productParam->icon,
                                                                        'name' => $paramItem->productParam->name,
                                                                    ];
                                                                }
                                                            }
                                                        }

                                                        // Сбор параметров из parameters
                                                        if ($item->parametrs) {
                                                            foreach ($item->parametrs as $parameter) {
                                                                if ($parameter->productParam->show_on_table) {
                                                                    $uniqueParamNames[$parameter->productParam->name] = [
                                                                        'icon' => $parameter->productParam->icon,
                                                                        'name' => $parameter->productParam->name,
                                                                    ];
                                                                }
                                                            }
                                                        }
                                                    }
                                                    $uniqueParamNames = $uniqueParamNames->unique();
                                                @endphp
                                                <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                                                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                                        <tr>
                                                            <th scope="col" class="px-6 py-3">
                                                                Артикул
                                                            </th>



                                                            <!-- Заголовки параметров -->
                                                            @foreach ($uniqueParamNames as $paramName)
                                                                <th scope="col" class="px-6 py-3">
                                                                    @if ($paramName['icon'])
                                                                        <img src="{{ Storage::disk(config('filesystems.default'))->url($paramName['icon']) }}" class="min-w-10 max-w-10">
                                                                    @else
                                                                        {{ $paramName['name'] }}
                                                                    @endif
                                                                </th>
                                                            @endforeach
                                                            <th scope="col" class="px-6 py-3">
                                                                Цена
                                                            </th>
                                                            <th scope="col" class="px-6 py-3"></th>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($batch as $item)
                                                            <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 border-gray-200">
                                                                <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                                                    <a href="{{ route('client.product_detail', $item->slug) }}" class="flex items-center gap-2 text-blue-500" wire:navigate>
                                                                        {{ $item->sku }}
                                                                        <x-carbon-link class="w-4 h-4" />
                                                                    </a>
                                                                </th>
                                                                @foreach ($uniqueParamNames as $paramName)

                                                                    <td class="px-6 py-4 whitespace-nowrap">
                                                                        @php
                                                                            $paramValue = '';

                                                                            // Проверка paramItems
                                                                            if ($item->paramItems) {
                                                                                foreach ($item->paramItems as $paramItem) {
                                                                                    if ($paramItem->productParam &&
                                                                                        $paramItem->productParam->name === $paramName['name'] &&
                                                                                        $paramItem->productParam->show_on_table) {
                                                                                        $paramValue = $paramItem->title ?? '';
                                                                                        break;
                                                                                    }
                                                                                }
                                                                            }

                                                                            // Проверка parameters если значение еще не найдено
                                                                            if ($paramValue === '' && $item->parametrs) {

                                                                                foreach ($item->parametrs as $parameter) {
                                                                                    if ($parameter->productParam->name === $paramName['name'] && $parameter->productParam->show_on_table) {

                                                                                        $paramValue = $parameter->title ?? '';
                                                                                        break;
                                                                                    }
                                                                                }
                                                                            }
                                                                        @endphp
                                                                        {{ $paramValue }}
                                                                    </td>
                                                                @endforeach
                                                                <td class="px-6 py-4">
                                                                    <div class="flex flex-col gap-1 relative whitespace-nowrap">

                                                                        <div class="flex items-center gap-4">
                                                                            <span class="text-lg font-extrabold leading-tight text-gray-900 dark:text-white">
                                                                                {{ $item->new_price ?? $item->getActualPrice() }} ₽
                                                                            </span>
                                                                            @if($item->new_price)
                                                                                <div class="flex items-center gap-2">
                                                                                    <span class="text-md line-through font-regular leading-tight text-gray-600 dark:text-white">
                                                                                        {{ $item->getActualPrice() }} ₽
                                                                                    </span>
                                                                                    <div class="flex items-center justify-between gap-2">
                                                                                        @if($item->new_price)
                                                                                            <span class="me-2 rounded bg-green-600 px-2.5 py-0.5 text-xs font-medium text-white dark:bg-green-900 dark:text-white dark:text-white">
                                                                                                {{ round(100 - ($item->new_price * 100 / $item->getActualPrice())) }}%
                                                                                            </span>
                                                                                        @endif
                                                                                    </div>
                                                                                </div>
                                                                            @endif
                                                                        </div>
                                                                        @if (!auth()->user() && $item->auth_price)
                                                                            <div class="flex items-center self-start gap-2 bg-blue-600 text-white text-xs font-medium me-2 px-2.5 py-0.5 rounded dark:bg-blue-900 dark:text-white" x-data="{
                                                                                popover: false,
                                                                            }" @mouseover="popover = true"  @mouseover.away = "popover = false">
                                                                                <span>{{ $item->auth_price }} ₽</span>
                                                                                <x-carbon-information class="w-4 h-4" />

                                                                                <div role="tooltip" class="absolute bottom-0 right-[calc(100%+10px)] z-10 inline-block w-[400px] text-sm text-gray-500 transition-opacity duration-300 bg-white border border-gray-200 rounded-lg shadow-xs dark:text-gray-400 dark:border-gray-600 dark:bg-gray-800" x-show="popover">
                                                                                    <div class="px-3 py-2 bg-gray-100 border-b border-gray-200 rounded-t-lg dark:border-gray-600 dark:bg-gray-700">
                                                                                        <h3 class="font-semibold text-gray-900 dark:text-white whitespace-break-spaces">Цена для авторизованных пользователей</h3>
                                                                                    </div>
                                                                                    <div class="px-3 py-2">
                                                                                        <p class="whitespace-break-spaces">Эта цена доступна для авторизованных пользователей. <a class="text-blue-500" href="{{ route('login') }}" wire:navigate>Войдите</a> или <a class="text-blue-500" href="{{ route('register') }}" wire:navigate>зарегистрируйтесь</a> для применения этой цены.</p>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        @endif
                                                                    </div>
                                                                </td>
                                                                <td class="px-6 py-4">
                                                                    <div class="flex items-center justify-end gap-4" x-data="{
                                                                        count: 1,
                                                                        init() {
                                                                            this.watchCountState();
                                                                        },
                                                                        watchCountState() {
                                                                            $watch('count', () => {this.validate()});
                                                                        },
                                                                        validate() {
                                                                            if (this.count < 1) this.count = 1;
                                                                        }
                                                                    }">
                                                                        <div class="relative flex items-center max-w-[8rem]">
                                                                            <button type="button" class="bg-gray-100 dark:bg-gray-700 dark:hover:bg-gray-600 dark:border-gray-600 hover:bg-gray-200 border border-gray-300 rounded-s-lg p-2 h-9 focus:ring-gray-100 dark:focus:ring-gray-700 focus:ring-2 focus:outline-none"  @click="count--">
                                                                                <svg class="w-2 h-2 text-gray-900 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 2">
                                                                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 1h16"/>
                                                                                </svg>
                                                                            </button>
                                                                            <input type="number" class="[-moz-appearance:_textfield] [&::-webkit-outer-spin-button]:m-0 [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:m-0 [&::-webkit-inner-spin-button]:appearance-none bg-gray-50 border-x-0 border-gray-300 h-9 text-center text-gray-900 text-sm focus:ring-blue-500 focus:border-blue-500 block w-full py-1.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 min-w-10" placeholder="999" x-model="count" />
                                                                            <button type="button" class="bg-gray-100 dark:bg-gray-700 dark:hover:bg-gray-600 dark:border-gray-600 hover:bg-gray-200 border border-gray-300 rounded-e-lg p-2 h-9 focus:ring-gray-100 dark:focus:ring-gray-700 focus:ring-2 focus:outline-none" @click="count++">
                                                                                <svg class="w-3 h-3 text-gray-900 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 18">
                                                                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 1v16M1 9h16"/>
                                                                                </svg>
                                                                            </button>
                                                                        </div>
                                                                        <button type="button"
                                                                        class="inline-flex items-center rounded-lg bg-blue-500 px-5 py-2.5 text-sm font-medium text-white hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-300 dark:bg-blue-500 dark:hover:bg-blue-500 dark:focus:ring-blue-800"
                                                                        @click="$store.cart.addVariationToCart({
                                                                            count: count,
                                                                            variationId: {{ $item->id }},
                                                                            name: '{{ $item->name }}'
                                                                        });">
                                                                            <svg class="h-5 w-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                                                                width="24" height="24" fill="none" viewBox="0 0 24 24">
                                                                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                                                                    stroke-width="2"
                                                                                    d="M4 4h1.5L8 16m0 0h8m-8 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4Zm8 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4Zm.75-3H7.5M11 7H6.312M17 4v6m-3-3h6" />
                                                                            </svg>
                                                                        </button>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>

                                                </table>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="flex flex-col items-center justify-center p-8 text-center">
                                            <div class="mb-4">
                                                <svg class="w-12 h-12 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                            </div>
                                            <h3 class="mb-2 text-xl font-semibold text-gray-900 dark:text-white">Ничего не найдено</h3>
                                            <p class="text-gray-500 dark:text-gray-400">По выбранным фильтрам товары не найдены. Попробуйте изменить параметры поиска.</p>
                                            <button wire:click="resetFilters" class="mt-4 inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:ring-4 focus:outline-none focus:ring-blue-300 dark:bg-blue-500 dark:hover:bg-blue-600 dark:focus:ring-blue-800">
                                                <svg class="w-4 h-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                                </svg>
                                                Сбросить фильтры
                                            </button>
                                        </div>
                                    @endforelse
                                </div>
                            @endif

                        </div>
                    @endif


                </div>
            </div>
        </div>
    @endif
</section>

<script>
    document.addEventListener('livewire:initialized', () => {
        Livewire.on('filter-reset', () => {
            setTimeout(() => {
                const url = new URL(window.location.href);
                const path = url.pathname;

                window.history.pushState({}, '', path);
            }, 0);
        });
    });
</script>
