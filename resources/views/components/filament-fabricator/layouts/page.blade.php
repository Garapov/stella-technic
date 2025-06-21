@props(['page'])
<x-guest-layout>
    <section class="py-8 bg-white md:py-10 dark:bg-gray-900 antialiased">
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

    <x-filament-fabricator::page-blocks :blocks="$page->blocks" />

    <x-floating-control-panel>
        <livewire:general.panel.clear-page-cache-button />

        @if ($page->id)
            <livewire:general.panel.edit-resource-button link="{{ \Z3d0X\FilamentFabricator\Resources\PageResource::getUrl('edit', ['record' => $page->id ]) }}" title="Редактировать страницу" />
        @endif
    </x-floating-control-panel>
</x-guest-layout>