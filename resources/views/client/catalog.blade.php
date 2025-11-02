<x-guest-layout>
    {{-- <livewire:catalog.items :path="$path" :display_filter="true"  /> --}}
    <livewire:catalog.items-lazy />
    <livewire:main.articles />
    <livewire:main.customers />
    <livewire:main.news />

    <x-floating-control-panel>
        <livewire:general.panel.clear-page-cache-button />


        <livewire:general.panel.edit-resource-button link="{{ \App\Filament\Resources\ProductCategoryResource::getUrl('edit', ['record' =>  Request::segment(count(Request::segments()))]) }}" title="Редактировать категорию" />
    </x-floating-control-panel>
</x-guest-layout>