<div>
    @if (count($products) > 0)
        <section class="py-10 px-4 dark:bg-dark" id="paginated-products">
            <div class=" lg:container lg:mx-auto">
                <div class="flex items-center justify-between mb-10">
                    <p class="lg:text-4xl text-xl text-slate-900 dark:text-white font-semibold">Популярные товары</p>
                    <div class="flex items-center gap-8">
                        <a href="{{ route('client.catalog.popular') }}"
                            class="inline-flex items-center font-medium text-blue-600 dark:text-blue-500 hover:underline" wire:navigate>
                            Смотреть все
                            <x-fas-arrow-right class="w-4 h-4 ms-2 rtl:rotate-180" />
                        </a>
                    </div>
                </div>
                <div class="mt-4 grid gap-4 grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                    
                    @foreach ($products as $variant)
                        @livewire('general.product-variant', [
                            'variant' => $variant
                        ], key($variant->id))
                    @endforeach
                </div>
            </div>
        </section>
    @endif
</div>