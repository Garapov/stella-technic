<div>
    @if (count($categories))
        <section class="py-10 dark:bg-dark overflow-hidden">
            <div class="lg:container p-4 lg:mx-auto">
                <div class="flex items-center justify-between mb-10">
                    <p class="lg:text-4xl text-xl text-slate-900 dark:text-white font-semibold">Категории товаров</p>
                </div>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    @foreach ($categories as $category)
                        @if (!$category->is_visible)
                            @continue
                        @endif
                        @livewire('general.category', [
                            'category' => $category,
                        ], key($category->id))
                    @endforeach
                </div>
            </div>
        </section>
    @endif
</div>