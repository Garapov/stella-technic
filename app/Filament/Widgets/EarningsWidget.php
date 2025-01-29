<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;

class EarningsWidget extends StatsOverviewWidget
{
    protected static ?string $pollingInterval = '30s';
    protected int | string | array $columnSpan = [
        'sm' => 1,
        'md' => 1,
        'lg' => 2,
        'xl' => 2,
    ];
    protected static ?int $sort = 3;

    protected function getStats(): array
    {
        $currentMonth = now()->month;
        $lastMonth = now()->subMonth()->month;

        // Текущий месяц
        $currentMonthEarnings = Order::query()
            ->where('status', Order::STATUS_DELIVERED)
            ->whereMonth('created_at', $currentMonth)
            ->sum('total_price');

        // Прошлый месяц
        $lastMonthEarnings = Order::query()
            ->where('status', Order::STATUS_DELIVERED)
            ->whereMonth('created_at', $lastMonth)
            ->sum('total_price');

        // За все время
        $totalEarnings = Order::query()
            ->where('status', Order::STATUS_DELIVERED)
            ->sum('total_price');

        // Вычисляем процент изменения
        $percentageChange = $lastMonthEarnings != 0 
            ? (($currentMonthEarnings - $lastMonthEarnings) / $lastMonthEarnings) * 100 
            : 0;

        return [
            Stat::make('Доход за текущий месяц', number_format($currentMonthEarnings, 0, '.', ' ') . ' ₽')
                ->description($percentageChange >= 0 
                    ? 'Увеличение на ' . number_format(abs($percentageChange), 1) . '%'
                    : 'Уменьшение на ' . number_format(abs($percentageChange), 1) . '%')
                ->descriptionIcon($percentageChange >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($percentageChange >= 0 ? 'success' : 'danger'),

            Stat::make('Доход за прошлый месяц', number_format($lastMonthEarnings, 0, '.', ' ') . ' ₽'),

            Stat::make('Общий доход', number_format($totalEarnings, 0, '.', ' ') . ' ₽'),
        ];
    }

    protected function getChartData($query, int $days): array
    {
        return $query
            ->where('created_at', '>=', now()->subDays($days))
            ->selectRaw('DATE(created_at) as date, SUM(total_price) as total')
            ->groupBy('date')
            ->pluck('total')
            ->toArray();
    }

    // protected function getColumns(): int
    // {
    //     return 1;
    // }
}
