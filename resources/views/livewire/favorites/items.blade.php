<section class="py-8 bg-white md:py-16 dark:bg-gray-900 antialiased">
    <div class="mx-auto container">
        <!-- Heading & Filters -->

        <div class="mb-4 items-end justify-between space-y-4 sm:flex sm:space-y-0 md:mb-8">
            <div>
                @livewire('general.breadcrumbs')
                <h2 class="mt-3 text-xl font-semibold text-gray-900 dark:text-white sm:text-2xl">Избранное</h2>
            </div>
        </div>
        <div class="mb-4 grid gap-4 sm:grid-cols-2 md:mb-8 lg:grid-cols-3 xl:grid-cols-4">
            @livewire('general.product')
            @livewire('general.product')
            @livewire('general.product')
            @livewire('general.product')
            @livewire('general.product')
            @livewire('general.product')
            @livewire('general.product')
            @livewire('general.product')
        </div>
        <div class="w-full text-center">
            <button type="button"
                class="rounded-lg border border-gray-200 bg-white px-5 py-2.5 text-sm font-medium text-gray-900 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:outline-none focus:ring-4 focus:ring-gray-100 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white dark:focus:ring-gray-700">Загрузить
                еще</button>
        </div>
    </div>
</section>
