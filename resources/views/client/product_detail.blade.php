<x-guest-layout>
    <section class="py-8 bg-white md:py-10 dark:bg-gray-900 antialiased">
        <div class="container px-4 mx-auto 2xl:px-0">
            <div class="mb-10">@livewire('general.breadcrumbs')</div>
            @livewire('product.detail', ['slug' => $product_slug])
        </div>
    </section>
    @livewire('main.articles')
    @livewire('main.customers')
    @livewire('main.news')
</x-guest-layout>
