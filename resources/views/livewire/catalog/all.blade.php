<div>
    @if (count($categories))
        <section class="py-10 dark:bg-dark overflow-hidden">
            <div class="container mx-auto">
                <div class="flex items-center justify-between mb-10">
                    <p class="lg:text-4xl text-xl text-slate-900 dark:text-white font-semibold">Категории товаров</p>
                </div>
                
                <!-- ====== Categories Section Start -->
                <div class="grid grid-cols-4 gap-4">
                    @foreach ($categories as $category)
                        @livewire('general.category', [
                            'category' => $category,
                        ], key($category->id))
                    @endforeach
                </div>
                <!-- ====== Categories Section End -->
                
            </div>
        </section>
    @endif
</div>