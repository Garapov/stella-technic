<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Widgets\Widget;
use Illuminate\Support\Carbon;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Support\Concerns\EvaluatesClosures;

class EarningsWidget extends Widget
{
    use InteractsWithActions;
    use InteractsWithForms;
    use EvaluatesClosures;

    protected static string $view = 'filament.widgets.earnings-widget';
    
    protected int | string | array $columnSpan = 'full';

    public string $period = 'all';

    protected function getViewData(): array
    {
        return [
            'earnings' => $this->getEarnings(),
            'period' => $this->period,
        ];
    }

    public function changePeriod(string $period): void
    {
        $this->period = $period;
        $this->dispatch('earnings-updated');
    }

    protected function getEarnings(): float
    {
        $query = Order::where('status', 'delivered');

        return match ($this->period) {
            'day' => $query->whereDate('created_at', Carbon::today())->sum('total_price'),
            'week' => $query->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->sum('total_price'),
            'month' => $query->whereMonth('created_at', Carbon::now()->month)->sum('total_price'),
            default => $query->sum('total_price'),
        };
    }

    public function mount(): void
    {
        $this->period = 'all';
    }
}
