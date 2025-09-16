<x-guest-layout>
    @php
        $categories = \App\Models\ProductCategory::where('parent_id', -1)->get();

        $allCategoryIds = \App\Models\ProductCategory::all()->pluck('id')->toArray();

        // Получаем счётчики вариантов
        $variationCounts = \App\Models\ProductVariant::selectRaw('count(*) as count, product_product_category.product_category_id')
            ->join('products', 'products.id', '=', 'product_variants.product_id')
            ->join('product_product_category', 'products.id', '=', 'product_product_category.product_id')
            ->whereIn('product_product_category.product_category_id', $allCategoryIds)
            ->groupBy('product_product_category.product_category_id')
            ->pluck('count', 'product_product_category.product_category_id');


        // Получаем минимальные цены
        $minPrices = \App\Models\ProductVariant::selectRaw('MIN(COALESCE(product_variants.new_price, product_variants.price)) as min_price, product_product_category.product_category_id')
            ->join('products', 'products.id', '=', 'product_variants.product_id')
            ->join('product_product_category', 'products.id', '=', 'product_product_category.product_id')
            ->whereIn('product_product_category.product_category_id', $allCategoryIds)
            ->groupBy('product_product_category.product_category_id')
            ->pluck('min_price', 'product_product_category.product_category_id');
    @endphp

    <section class="py-4 bg-white md:py-4 dark:bg-gray-900 antialiased">
        <div class="xl:px-[100px] px-[20px]">
            <!-- Heading & Filters -->
    
            <div class="items-end justify-between space-y-4 sm:flex sm:space-y-0">
                <div>
                    {{ Breadcrumbs::render('catalog') }}
                    <h2 class="mt-3 text-xl font-semibold text-gray-900 dark:text-white sm:text-2xl">Каталог</h2>
                </div>
            </div>

            @if (count($categories))
                
                <div class="py-10 dark:bg-dark">
                    <div class="">
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            @foreach ($categories as $category)
                                @livewire('general.category', [
                                    'category' => $category,
                                    'counts' => $variationCounts,
                                    'minPrices' => $minPrices,
                                    'show_counts' => true,
                                    'show_price' => true
                                ], key($category->id))
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
            
        </div>
    </section>
    
    
    @livewire('main.articles')
    @livewire('main.customers')
    @livewire('main.news')
</x-guest-layout>