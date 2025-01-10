<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class OrderStatsWidget extends BaseWidget
{
    protected static ?string $pollingInterval = '30s';

    protected function getStats(): array
    {
        return [
            Stat::make('Всего заказов', Order::count())
                ->description('За все время')
                ->descriptionIcon('heroicon-m-shopping-cart')
                ->color('success'),
            
            Stat::make('Заказы за сегодня', Order::whereDate('created_at', today())->count())
                ->description('Заказы размещенные сегодня')
                ->descriptionIcon('heroicon-m-calendar')
                ->color('warning'),
            
            Stat::make('Ожидающие заказы', Order::where('status', 'pending')->count())
                ->description('Заказы в обработке')
                ->descriptionIcon('heroicon-m-clock')
                ->color('danger'),
        ];
    }
}
