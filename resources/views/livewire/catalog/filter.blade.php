<div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-900">
    <div>
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Фильтры</h3>
        </div>

        @foreach($parameters as $paramName => $params)
            <div class="mb-6">
                <h4 class="text-md font-medium mb-3">{{ $paramName }}</h4>
                <div class="space-y-2">
                    @foreach($params as $paramItemId => $paramData)
                        @if($paramData['type'] === 'color')
                            <div class="flex items-center">
                                <input 
                                    type="checkbox" 
                                    id="param_{{ $paramItemId }}"
                                    wire:model.live="filters.product_param_items.{{ $paramData['param_id'] }}"
                                    value="{{ $paramItemId }}"
                                    class="mr-2"
                                >
                                <label for="param_{{ $paramItemId }}" class="flex items-center">
                                    {{ $paramData['title'] }}
                                    <span 
                                        class="ml-2 w-4 h-4 inline-block rounded-full" 
                                        style="background-color: {{ $paramData['value'] }}"
                                    ></span>
                                </label>
                            </div>
                        @else
                            <div class="flex items-center">
                                <input 
                                    type="checkbox" 
                                    id="param_{{ $paramItemId }}"
                                    wire:model.live="filters.product_param_items.{{ $paramData['param_id'] }}"
                                    value="{{ $paramItemId }}"
                                    class="mr-2"
                                >
                                <label for="param_{{ $paramItemId }}">
                                    {{ $paramData['title'] }}
                                </label>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>
</div> 