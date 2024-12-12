@props(['page'])
<x-guest-layout>
    <section class="py-8 bg-white md:py-16 dark:bg-gray-900 antialiased">
        <div class="mx-auto container">
            <!-- Heading & Filters -->
    
            <div class="items-end justify-between space-y-4 sm:flex sm:space-y-0">
                <div>
                    @php
                        logger()->info('Page in template:', [
                            'title' => $page->title ?? 'No title',
                            'slug' => $page->slug ?? 'No slug'
                        ]);
                    @endphp
                    @livewire('general.breadcrumbs')
                    <h2 class="mt-3 text-xl font-semibold text-gray-900 dark:text-white sm:text-2xl">{{ $page->title }}</h2>
                </div>
            </div>
            
        </div>
    </section>
    <x-filament-fabricator::page-blocks :blocks="$page->blocks" />
</x-guest-layout>