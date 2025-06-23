<x-guest-layout>
    @php
        $categories = \App\Models\ProductCategory::where('parent_id', -1)->get();
    @endphp

    <section class="py-8 bg-white md:py-10 dark:bg-gray-900 antialiased">
        <div class="mx-auto container">
            <!-- Heading & Filters -->
    
            <div class="items-end justify-between space-y-4 sm:flex sm:space-y-0">
                <div>
                    {{ Breadcrumbs::render('catalog') }}
                    <h2 class="mt-3 text-xl font-semibold text-gray-900 dark:text-white sm:text-2xl">Каталог</h2>
                </div>
            </div>

            @if (count($categories))
                <!-- ====== Categories Section Start -->
                <div class="py-10 dark:bg-dark">
                    <div class="container mx-auto">
                        <div class="grid grid-cols-4 gap-4">
                            @foreach ($categories as $category)
                                @livewire('general.category', [
                                    'category' => $category,
                                ], key($category->id))
                            @endforeach
                        </div>
                    </div>
                </div>
                <!-- ====== Categories Section End -->
            @endif
            
        </div>
    </section>
    
    
    @livewire('main.articles')
    @livewire('main.customers')
    @livewire('main.news')
</x-guest-layout>