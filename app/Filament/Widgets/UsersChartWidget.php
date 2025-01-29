<?php

namespace App\Filament\Widgets;

use App\Models\User;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class UsersChartWidget extends ChartWidget
{
    protected static ?string $heading = 'Статистика пользователей';
    protected int | string | array $columnSpan = [
        'sm' => 1,
        'md' => 1,
        'lg' => 1,
        'xl' => 1,
    ];
    protected static ?string $maxHeight = '400px';
    protected static ?int $sort = 2;
    
    public ?string $filter = 'day';

    protected function getFilters(): ?array
    {
        return [
            'all' => 'За все время',
            'month' => 'За месяц',
            'week' => 'За неделю',
            'day' => 'За день',
        ];
    }

    protected function getData(): array
    {
        $data = match ($this->filter) {
            'day' => $this->getDailyData(),
            'week' => $this->getWeeklyData(),
            'month' => $this->getMonthlyData(),
            default => $this->getAllTimeData(),
        };

        return [
            'datasets' => [
                [
                    'label' => 'Новые пользователи',
                    'data' => $data['values'],
                    'borderColor' => 'rgb(75, 192, 192)',
                    'backgroundColor' => 'rgba(75, 192, 192, 0.5)',
                    'tension' => 0.3,
                    'fill' => true,
                ],
            ],
            'labels' => $data['labels'],
        ];
    }

    protected function getDailyData(): array
    {
        $today = now()->startOfDay();
        
        $users = User::query()
            ->whereDate('created_at', $today)
            ->get()
            ->groupBy(function ($user) {
                return $user->created_at->format('H');
            })
            ->map(function ($users) {
                return $users->count();
            });

        $hours = range(0, 23);
        $values = [];
        $labels = [];

        foreach ($hours as $hour) {
            $hourStr = str_pad($hour, 2, '0', STR_PAD_LEFT);
            $values[] = $users->get($hourStr, 0);
            $labels[] = "{$hourStr}:00";
        }

        return [
            'values' => $values,
            'labels' => $labels,
        ];
    }

    protected function getWeeklyData(): array
    {
        $startOfWeek = now()->startOfWeek();
        $endOfWeek = now()->endOfWeek();
        
        $users = User::query()
            ->whereBetween('created_at', [$startOfWeek, $endOfWeek])
            ->get()
            ->groupBy(function ($user) {
                return $user->created_at->format('Y-m-d');
            })
            ->map(function ($users) {
                return $users->count();
            });

        $values = [];
        $labels = [];
        $current = $startOfWeek->copy();

        while ($current <= $endOfWeek) {
            $dateKey = $current->format('Y-m-d');
            $values[] = $users->get($dateKey, 0);
            $labels[] = $current->isoFormat('dd');
            $current->addDay();
        }

        return [
            'values' => $values,
            'labels' => $labels,
        ];
    }

    protected function getMonthlyData(): array
    {
        $startOfMonth = now()->startOfMonth();
        $endOfMonth = now()->endOfMonth();
        
        $users = User::query()
            ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->get()
            ->groupBy(function ($user) {
                return $user->created_at->weekOfMonth;
            })
            ->map(function ($users) {
                return $users->count();
            });

        $values = [];
        $labels = [];
        $currentWeek = 1;
        $lastWeek = $endOfMonth->weekOfMonth;

        while ($currentWeek <= $lastWeek) {
            $values[] = $users->get($currentWeek, 0);
            $labels[] = "Неделя {$currentWeek}";
            $currentWeek++;
        }

        return [
            'values' => $values,
            'labels' => $labels,
        ];
    }

    protected function getAllTimeData(): array
    {
        $sixMonthsAgo = now()->subMonths(6)->startOfMonth();
        
        $users = User::query()
            ->where('created_at', '>=', $sixMonthsAgo)
            ->get()
            ->groupBy(function ($user) {
                return $user->created_at->format('Y-m');
            })
            ->map(function ($users) {
                return $users->count();
            });

        $values = [];
        $labels = [];
        $current = $sixMonthsAgo->copy();

        while ($current <= now()) {
            $monthKey = $current->format('Y-m');
            $values[] = $users->get($monthKey, 0);
            $labels[] = $current->isoFormat('MMM YYYY');
            $current->addMonth();
        }

        return [
            'values' => $values,
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => false,
                ],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'stepSize' => 1,
                    ],
                ],
            ],
        ];
    }
}
