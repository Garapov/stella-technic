<div class="p-3 bg-white border border-gray-200 rounded-lg shadow-sm dark:bg-gray-900 dark:border-gray-700 flex flex-col gap-3" wire:loading.class="opacity-50 pointer-events-none" x-data="{
    isOpened: true
}">
    <div class="flex items-center justify-between cursor-pointer" @click="isOpened = !isOpened">
        <span class="text-[0.9rem] font-semibold dark:text-white">{{ $paramName }}</span>
        <span class="block min-w-6 max-w-6 min-h-6 max-h-6" :class="{'rotate-180': isOpened}">
            <x-eva-arrow-ios-downward-outline class="w-full h-full"  />
        </span>
    </div>
    <ul class="space-y-2 text-sm" aria-labelledby="dropdownDefault" x-data="{ showAll: false }" x-show="isOpened">
        
        @foreach($paramGroup->sortBy('value')->sortBy('sort')->flatten() as $key => $paramData)
            <li class="flex items-center @unless(in_array($paramData->id, $availableParams)) opacity-30 cursor-not-allowed pointer-events-none @endunless" @if($key > 4) x-show="showAll" @endif>
                <input
                    type="checkbox"
                    id="param_{{ $paramData->id }}"
                    value="{{ $paramData->id }}"
                    class="w-5 h-5 bg-gray-100 border-gray-300 rounded text-primary-600 focus:ring-primary-500 dark:focus:ring-primary-600 dark:ring-offset-gray-700 focus:ring-2 dark:bg-gray-600 dark:border-gray-500"
                    wire:model.live="filters.parametrs.$hasid"
                />
                <label for="param_{{ $paramData->id }}" class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-100">
                    {{ $paramData['title'] }}
                </label>
            </li>
        @endforeach

        @if (count($paramGroup) > 5)
            <li @click="showAll = !showAll" class="cursor-pointer font-medium text-blue-600 dark:text-blue-500 hover:underline">
                Показать <template x-if="!showAll"><span>больше</span></template><template x-if="showAll"><span>меньше</span></template>
            </li>
        @endif
    </ul>
</div>