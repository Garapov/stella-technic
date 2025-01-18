<?php

namespace App\Filament\Widgets;

use App\Models\User;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class UsersChartWidget extends ChartWidget
{
    protected static ?string $heading = 'Статистика пользователей';
    protected int | string | array $columnSpan = [
        'sm' => 1,
        'md' => 1,
        'lg' => 1,
        'xl' => 1,
    ];
    
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
        $data = User::query()
            ->whereDate('created_at', Carbon::today())
            ->select(DB::raw("strftime('%H', created_at) as hour"), DB::raw('COUNT(*) as total'))
            ->groupBy('hour')
            ->orderBy('hour')
            ->get();

        $hours = range(0, 23);
        $values = [];
        $labels = [];

        foreach ($hours as $hour) {
            $values[] = $data->firstWhere('hour', sprintf('%02d', $hour))?->total ?? 0;
            $labels[] = sprintf('%02d:00', $hour);
        }

        return [
            'values' => $values,
            'labels' => $labels,
        ];
    }

    protected function getWeeklyData(): array
    {
        $data = User::query()
            ->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
            ->select(DB::raw("date(created_at) as date"), DB::raw('COUNT(*) as total'))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $days = [];
        $values = [];
        $labels = [];
        $current = Carbon::now()->startOfWeek();
        $end = Carbon::now()->endOfWeek();

        while ($current <= $end) {
            $date = $current->format('Y-m-d');
            $values[] = $data->firstWhere('date', $date)?->total ?? 0;
            $labels[] = $current->format('D');
            $current->addDay();
        }

        return [
            'values' => $values,
            'labels' => $labels,
        ];
    }

    protected function getMonthlyData(): array
    {
        $data = User::query()
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->select(DB::raw("strftime('%W', created_at) as week"), DB::raw('COUNT(*) as total'))
            ->groupBy('week')
            ->orderBy('week')
            ->get();

        $values = [];
        $labels = [];
        $currentWeek = Carbon::now()->startOfMonth()->copy();
        $endOfMonth = Carbon::now()->endOfMonth();

        while ($currentWeek <= $endOfMonth) {
            $weekNumber = $currentWeek->format('W');
            $values[] = $data->firstWhere('week', $weekNumber)?->total ?? 0;
            $labels[] = 'Неделя ' . $currentWeek->weekOfMonth;
            $currentWeek->addWeek();
        }

        return [
            'values' => $values,
            'labels' => $labels,
        ];
    }

    protected function getAllTimeData(): array
    {
        $data = User::query()
            ->select(
                DB::raw("strftime('%Y', created_at) as year"),
                DB::raw("strftime('%m', created_at) as month"),
                DB::raw('COUNT(*) as total')
            )
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();

        $monthNames = [
            'Янв', 'Фев', 'Мар', 'Апр', 'Май', 'Июн',
            'Июл', 'Авг', 'Сен', 'Окт', 'Ноя', 'Дек'
        ];

        $values = [];
        $labels = [];

        foreach ($data as $record) {
            $values[] = $record->total;
            $labels[] = $monthNames[(int)$record->month - 1] . ' ' . $record->year;
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
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'stepSize' => 1,
                    ],
                ],
            ],
            'plugins' => [
                'legend' => [
                    'display' => false,
                ],
            ],
        ];
    }
}
