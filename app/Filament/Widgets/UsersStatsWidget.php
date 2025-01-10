<?php

namespace App\Filament\Widgets;

use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;

class UsersStatsWidget extends BaseWidget
{
    protected static ?string $pollingInterval = '30s';
    protected int | string | array $columnSpan = [
        'sm' => 1,
        'md' => 1,
        'lg' => 2,
        'xl' => 2,
    ];
    protected static ?int $sort = 4;

    protected function getStats(): array
    {
        return [
            Stat::make('Всего пользователей', User::count())
                ->description('Общее количество пользователей')
                ->descriptionIcon('heroicon-m-users')
                ->color('success'),
            
            Stat::make('Новые пользователи', User::whereDate('created_at', today())->count())
                ->description('Зарегистрировались сегодня')
                ->descriptionIcon('heroicon-m-user-plus')
                ->color('warning'),
            
            Stat::make('За 7 дней', User::where('created_at', '>=', now()->subDays(7))->count())
                ->description('Новые пользователи за неделю')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('primary'),
        ];
    }

    // protected function getColumns(): int
    // {
    //     return 1;
    // }
}
