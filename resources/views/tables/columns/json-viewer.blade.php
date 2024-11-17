@php
    $state = json_decode($getState());
    $is_valid_json = json_last_error() == JSON_ERROR_NONE;
@endphp
<div class="w-full px-3 py-3.5">
    @if ($is_valid_json)
        @foreach ($state as $item)
            <div class="flex align-center justify-between">
                <div class="text-sm font-bold">
                    {{  $item->label }}:
                </div>
                <div class="ms-4 text-sm">{{ $item->value }}</div>
            </div>
        @endforeach
    @else
        <div class="text-xs text-red-600">
            Параметр не является JSON
        </div>
    @endif
    {{-- {{ gettype($state) }} --}}
</div>
