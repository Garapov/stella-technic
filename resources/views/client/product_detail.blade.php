<x-guest-layout>
    <section class="py-8 bg-white md:py-10 dark:bg-gray-900 antialiased">
        <div class="lg:container px-4 lg:mx-auto 2xl:px-0">
            <div class="mb-10">@livewire('general.breadcrumbs')</div>
            @livewire('product.detail', ['slug' => $product_slug])
        </div>
    </section>

</x-guest-layout>
