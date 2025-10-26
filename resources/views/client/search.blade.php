<x-guest-layout>
    {{-- <section class="py-4 bg-white md:py-4 dark:bg-gray-900 antialiased">
        <div class="xl:px-[100px] px-[20px]">
            <!-- Heading & Filters -->

            <div class="items-end justify-between space-y-4 sm:flex sm:space-y-0">
                <div>
                    @livewire('general.breadcrumbs')
                    <h2 class="mt-3 text-xl font-semibold text-gray-900 dark:text-white sm:text-2xl">Поиск
                    </h2>
                </div>
            </div>

        </div>
    </section> --}}
    <div class="xl:px-[100px] px-[20px] py-4">
        @livewire('search.results')
        @livewire('general.recently')
    </div>
    @livewire('main.popular')
</x-guest-layout>
