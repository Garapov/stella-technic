@props(['page'])
<x-guest-layout>
    <section class="py-8 bg-white md:py-16 dark:bg-gray-900 antialiased">
        <div class="mx-auto container">
            <!-- Heading & Filters -->
    
            <div class="items-end justify-between space-y-4 sm:flex sm:space-y-0">
                <div>
                    @livewire('general.breadcrumbs')
                    <h2 class="mt-3 text-xl font-semibold text-gray-900 dark:text-white sm:text-2xl">{{ $page->title }}</h2>
                </div>
            </div>
            
        </div>
    </section>
    <div class="container mx-auto px-4 py-8">
        <x-filament-fabricator::page-blocks :blocks="$page->blocks" />
    </div>
</x-guest-layout>