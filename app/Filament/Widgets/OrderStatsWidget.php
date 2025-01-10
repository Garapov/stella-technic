<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;

class OrderStatsWidget extends BaseWidget
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
        return [
            Stat::make('Всего заказов', Order::count())
                ->description('Общее количество заказов')
                ->descriptionIcon('heroicon-m-shopping-bag')
                ->color('success'),
            
            Stat::make('Новые заказы', Order::whereDate('created_at', today())->count())
                ->description('За сегодня')
                ->descriptionIcon('heroicon-m-shopping-cart')
                ->color('warning'),
            
            Stat::make('Выполнено', Order::where('status', 'delivered')->count())
                ->description('Доставленные заказы')
                ->descriptionIcon('heroicon-m-check-badge')
                ->color('success'),
        ];
    }

    // protected function getColumns(): int
    // {
    //     return 1;
    // }
}
