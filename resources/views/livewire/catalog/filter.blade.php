<div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-900">
    <div>
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Фильтры</h3>
        </div>
        <input type="checkbox" wire:model.live="filters.isPopular.$eq" value="Bike" />
       {{ print_r($filters) }}
    </div>
</div> 