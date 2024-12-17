<x-guest-layout>
    <section class="py-8 bg-white md:py-16 dark:bg-gray-900 antialiased">
        <div class="mx-auto container">
            <!-- Heading & Filters -->

            <div class="items-end justify-between space-y-4 sm:flex sm:space-y-0">
                <div>
                    @livewire('general.breadcrumbs')
                    <h2 class="mt-3 text-xl font-semibold text-gray-900 dark:text-white sm:text-2xl">Поиск
                    </h2>
                </div>
            </div>

        </div>
    </section>
    <div class="container mx-auto py-8">
        @livewire('search.results')
    </div>
</x-guest-layout>
