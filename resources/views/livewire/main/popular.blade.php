<div>
    @if (count($products) > 0)
        <section class="py-10 dark:bg-dark" id="paginated-products">
            <div class="container mx-auto">
                <div class="flex items-center justify-between mb-10">
                    <p class="text-4xl text-gray-900 dark:text-white">Популярные товары</p>
                    <div class="flex items-center gap-8">
                        <a href="{{ route('client.catalog.popular') }}"
                            class="inline-flex items-center font-medium text-blue-600 dark:text-blue-500 hover:underline" wire:navigate>
                            Смотреть все
                            <x-fas-arrow-right class="w-4 h-4 ms-2 rtl:rotate-180" />
                        </a>
                    </div>
                </div>
                <div class="mt-4 grid gap-4 sm:grid-cols-2 md:mb-8 lg:grid-cols-3 xl:grid-cols-4">
                    
                    @foreach ($products as $variant)
                        @livewire('general.product-variant', [
                            'variant' => $variant
                        ], key($variant->id))
                        
                    @endforeach
                </div>
                {{ $products->links(data: ['scrollTo' => '#paginated-products']) }}
            </div>
        </section>
    @endif
</div>