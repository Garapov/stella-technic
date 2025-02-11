<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class EarningsChartWidget extends ChartWidget
{
    protected static ?string $heading = 'Статистика доходов';
    protected int | string | array $columnSpan = [
        'sm' => 1,
        'md' => 1,
        'lg' => 1,
        'xl' => 1,
    ];
    protected static ?string $maxHeight = '400px';
    protected static ?int $sort = 1;
    
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
                    'label' => 'Доход',
                    'data' => $data['values'],
                    'backgroundColor' => 'rgba(59, 130, 246, 0.5)',
                    'borderColor' => 'rgb(59, 130, 246)',
                    'borderWidth' => 1,
                ],
            ],
            'labels' => $data['labels'],
        ];
    }

    protected function getDailyData(): array
    {
        $today = now()->startOfDay();
        
        $orders = Order::query()
            // ->where('status', Order::STATUS_DELIVERED)
            ->whereDate('created_at', $today)
            ->get()
            ->groupBy(function ($order) {
                return $order->created_at->format('H');
            })
            ->map(function ($orders) {
                return $orders->sum('total_price');
            });

        $hours = range(0, 23);
        $values = [];
        $labels = [];

        foreach ($hours as $hour) {
            $hourStr = str_pad($hour, 2, '0', STR_PAD_LEFT);
            $values[] = $orders->get($hourStr, 0);
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
        
        $orders = Order::query()
            // ->where('status', Order::STATUS_DELIVERED)
            ->whereBetween('created_at', [$startOfWeek, $endOfWeek])
            ->get()
            ->groupBy(function ($order) {
                return $order->created_at->format('Y-m-d');
            })
            ->map(function ($orders) {
                return $orders->sum('total_price');
            });

        $values = [];
        $labels = [];
        $current = $startOfWeek->copy();

        while ($current <= $endOfWeek) {
            $dateKey = $current->format('Y-m-d');
            $values[] = $orders->get($dateKey, 0);
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
        
        $orders = Order::query()
            // ->where('status', Order::STATUS_DELIVERED)
            ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->get()
            ->groupBy(function ($order) {
                return $order->created_at->weekOfMonth;
            })
            ->map(function ($orders) {
                return $orders->sum('total_price');
            });

        $values = [];
        $labels = [];
        $currentWeek = 1;
        $lastWeek = $endOfMonth->weekOfMonth;

        while ($currentWeek <= $lastWeek) {
            $values[] = $orders->get($currentWeek, 0);
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
        
        $orders = Order::query()
            // ->where('status', Order::STATUS_DELIVERED)
            ->where('created_at', '>=', $sixMonthsAgo)
            ->get()
            ->groupBy(function ($order) {
                return $order->created_at->format('Y-m');
            })
            ->map(function ($orders) {
                return $orders->sum('total_price');
            });

        $values = [];
        $labels = [];
        $current = $sixMonthsAgo->copy();

        while ($current <= now()) {
            $monthKey = $current->format('Y-m');
            $values[] = $orders->get($monthKey, 0);
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
                        'callback' => "function(value) {
                            return value.toString().replace(/\\B(?=(\\d{3})+(?!\\d))/g, ' ') + ' ₽';
                        }",
                    ],
                ],
            ],
        ];
    }
}
