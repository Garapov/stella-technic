@aware(['page'])

<div class="bg-white dark:bg-gray-900 pt-4">
    <div class="container mx-auto">
        @if($title)
            <h2 class="text-2xl font-bold dark:text-white">{{ $title }}</h2>
        @endIf
    </div>
    @livewire('catalog.items', ['products' => $items, 'filter' => $filter])
    
</div>
