<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Редактирование категории') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="container mx-auto sm:px-6 lg:px-8">
            @livewire('dashboard.categories.edit', [
                'slug' => $slug
            ])
        </div>
    </div>
</x-app-layout>
