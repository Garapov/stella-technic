<x-filament::widget>
    <x-filament::card>
        <div class="flex items-center justify-between">
            <h2 class="text-lg font-bold tracking-tight">
                Общий доход
            </h2>
            <div class="flex gap-2">
                <x-filament::button
                    size="sm"
                    :color="$period === 'all' ? 'primary' : 'gray'"
                    wire:click="changePeriod('all')"
                >
                    За все время
                </x-filament::button>
                <x-filament::button
                    size="sm"
                    :color="$period === 'month' ? 'primary' : 'gray'"
                    wire:click="changePeriod('month')"
                >
                    За месяц
                </x-filament::button>
                <x-filament::button
                    size="sm"
                    :color="$period === 'week' ? 'primary' : 'gray'"
                    wire:click="changePeriod('week')"
                >
                    За неделю
                </x-filament::button>
                <x-filament::button
                    size="sm"
                    :color="$period === 'day' ? 'primary' : 'gray'"
                    wire:click="changePeriod('day')"
                >
                    За день
                </x-filament::button>
            </div>
        </div>

        <div class="mt-4">
            <div class="text-3xl font-bold" wire:poll.10s>
                {{ number_format($earnings, 0, '.', ' ') }} ₽
            </div>
            <p class="text-gray-500">
                @switch($period)
                    @case('day')
                        Доход за сегодня
                        @break
                    @case('week')
                        Доход за текущую неделю
                        @break
                    @case('month')
                        Доход за текущий месяц
                        @break
                    @default
                        Общий доход за все время
                @endswitch
            </p>
        </div>
    </x-filament::card>
</x-filament::widget>
