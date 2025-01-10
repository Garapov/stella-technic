<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

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
        $query = Order::where('status', 'delivered');
        
        $data = match ($this->filter) {
            'day' => $this->getDailyData($query),
            'week' => $this->getWeeklyData($query),
            'month' => $this->getMonthlyData($query),
            default => $this->getAllTimeData($query),
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

    protected function getDailyData($query): array
    {
        $data = $query
            ->whereDate('created_at', Carbon::today())
            ->select(DB::raw("strftime('%H', created_at) as hour"), DB::raw('SUM(total_price) as total'))
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

    protected function getWeeklyData($query): array
    {
        $data = $query
            ->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
            ->select(DB::raw("date(created_at) as date"), DB::raw('SUM(total_price) as total'))
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

    protected function getMonthlyData($query): array
    {
        $data = $query
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->select(DB::raw("strftime('%W', created_at) as week"), DB::raw('SUM(total_price) as total'))
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

    protected function getAllTimeData($query): array
    {
        $data = $query
            ->select(
                DB::raw("strftime('%Y', created_at) as year"),
                DB::raw("strftime('%m', created_at) as month"),
                DB::raw('SUM(total_price) as total')
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
        return 'bar';
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
