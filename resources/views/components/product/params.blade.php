<div class="flex flex-col gap-2 p-4 bg-white rounded-lg shadow-lg">
    <div class="text-md font-bold pb-2">
        Другие вариации товара:
    </div>
    <div class="parametrs">
        @foreach($groupedParams as $paramGroup)
            <div class="mb-6">
                @php
                    $activeParamName = collect(array_filter($paramGroup['values'], fn ($paramValue) => $paramValue['is_current']));
                @endphp
                {{-- <h3 class="text-lg sm:text-sm font-semibold text-slate-900 dark:text-white @if (count($activeParamName) < 1) hidden @endif">{{ $paramGroup['name'] }} @if ($activeParamName->first())({{$activeParamName->first()['title']}})@endif</h3> --}}
                <h3 class="text-lg sm:text-sm font-semibold text-slate-900 dark:text-white @if (count($activeParamName) < 1) hidden @endif">{{ $paramGroup['name'] }}</h3>
                <div class="flex flex-wrap gap-4 mt-2 @if (count($activeParamName) < 1) hidden @endif">
                    @foreach(collect($paramGroup['values'])->sortBy('sort') as $value)
                        @if($paramGroup['name'] === 'Цвет')
                            <a href="{{ route('client.catalog', $variation->product->variants->where('id', $value['variant_id'])->first()->urlChain()) }}" wire:navigate
                                @class([
                                    'relative flex items-center gap-2 border rounded-full',
                                    'border-blue-600' => $value['is_current'],
                                    'border-slate-300 hover:border-blue-600' => !$value['is_current'] && $value['is_available'],
                                    'border-slate-200 opacity-30' => !$value['is_available'],
                                ])

                                @if(!$value['is_available']) disabled @endif>
                                @php
                                    $colors = explode('|', $value['value']);
                                @endphp
                                <div class="relative w-8 h-8 rounded-full border @if($value['is_current']) border-blue-500 @else border-gray-300 @endif overflow-hidden">
                                    @if(count($colors) > 1)
                                        <div class="absolute inset-0">
                                            <div class="h-full w-1/2 float-left" style="background-color: {{ trim($colors[0]) }}"></div>
                                            <div class="h-full w-1/2 float-right" style="background-color: {{ trim($colors[1]) }}"></div>
                                        </div>
                                    @else
                                        <div class="absolute inset-0" style="background-color: {{ trim($colors[0]) }}"></div>
                                    @endif
                                </div>
                            </a>
                        @else
                            <a href="{{ route('client.catalog', $variation->product->variants->where('id', $value['variant_id'])->first()->urlChain()) }}"
                                @class([
                                    'px-2 py-2 text-sm flex items-center justify-center shrink-0 text-xs rounded-xl bg-white',
                                    'border-2 border-blue-500' => $value['is_current'],
                                    'border border-slate-200 hover:border-blue-500' => !$value['is_current'] && $value['is_available'],
                                    'border border-slate-200 opacity-30' => !$value['is_available'],
                                    'hidden' => !$value['is_current'] && $value['is_fixed'],
                                ]) wire:navigate >
                                {{ $value['title'] }}
                            </a>
                        @endif
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>
</div>