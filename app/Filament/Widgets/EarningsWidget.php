<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;

class EarningsWidget extends BaseWidget
{
    protected static ?string $pollingInterval = '30s';
    protected int | string | array $columnSpan = [
        'md' => 1,
        'xl' => 1,
    ];

    protected function getStats(): array
    {
        $query = Order::where('status', 'delivered');

        return [
            Stat::make('Общий доход', number_format($query->sum('total_price'), 0, '.', ' ') . ' ₽')
                ->description('За все время')
                ->descriptionIcon('heroicon-m-banknotes')
                ->chart($this->getChartData($query, 60))
                ->color('success'),
            
            Stat::make('Доход за месяц', number_format($query->whereMonth('created_at', Carbon::now()->month)->sum('total_price'), 0, '.', ' ') . ' ₽')
                ->description('За текущий месяц')
                ->descriptionIcon('heroicon-m-calendar')
                ->chart($this->getChartData($query, 30))
                ->color('warning'),
            
            Stat::make('Доход за неделю', number_format($query->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->sum('total_price'), 0, '.', ' ') . ' ₽')
                ->description('За текущую неделю')
                ->descriptionIcon('heroicon-m-chart-bar')
                ->chart($this->getChartData($query, 7))
                ->color('danger'),
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

    protected function getColumns(): int
    {
        return 1;
    }
}
