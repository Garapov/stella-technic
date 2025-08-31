@aware(['page'])

<div class="bg-white dark:bg-gray-900 pt-4">
    <div class="container mx-auto">
        @if($title)
            <h2 class="text-2xl font-bold dark:text-white">{{ $title }}</h2>
        @endIf
    </div>
    @if ($type == 'products')
        @livewire('catalog.items', ['products' => $items, 'display_filter' => $filter])
    @endif

    @if ($type == 'category')
        @livewire('catalog.items', ['slug' => $category, 'display_filter' => $filter])
    @endif

    @if ($type == 'filter')
        @php
            // Проверяем, что $parametrs - это массив
            $parameterIds = is_array($parametrs) ? $parametrs : [];

            // Получаем ID вариаций, у которых есть хотя бы один параметр из массива $parametrs
            $filteredVariantIds = [];

            if (!empty($parameterIds)) {
                // Получаем ID вариаций, которые имеют указанные параметры
                // в любом из отношений: paramItems или parametrs
                $filteredVariants = \App\Models\ProductVariant::where(function($query) use ($parameterIds) {
                    // Проверяем первое отношение (paramItems)
                    $query->whereHas('paramItems', function($subQuery) use ($parameterIds) {
                        $subQuery->whereIn('product_param_item_id', $parameterIds);
                    });

                    // Или второе отношение (parametrs)
                    $query->orWhereHas('parametrs', function($subQuery) use ($parameterIds) {
                        $subQuery->whereIn('product_param_item_id', $parameterIds);
                    });
                })->pluck('id')->toArray();

                $filteredVariantIds = $filteredVariants;
            }
        @endphp

        @livewire('catalog.items', ['products' => $filteredVariantIds, 'filter' => $filter])
    @endif

</div>
