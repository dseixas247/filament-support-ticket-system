<?php

namespace App\Filament\Widgets;

use App\Models\Ticket;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class TicketsOverviewChart extends ChartWidget
{
    protected int|string|array $columnSpan = 'full';
    
    protected static ?string $maxHeight = '300px';

    protected static ?string $heading = 'Tickets Overview';

    public ?string $filter = 'week';

    protected function getData(): array
    {
        $start = null;
        $end = null;
        $per = null;

        switch($this->filter){
            case 'week':
                $start = now()->startOfWeek();
                $end = now()->endOfWeek();
                $per = 'perDay';
                break;

            case 'month':
                $start = now()->startOfMonth();
                $end = now()->endOfMonth();
                $per = 'perDay';
                break;

            case 'year':
                $start = now()->startOfYear();
                $end = now()->endOfYear();
                $per = 'perMonth';
                break;

            case 'today':
                $start = now()->startOfDay();
                $end = now()->endOfDay();
                $per = 'perHour';
                break;
        };

        $data = Trend::model(Ticket::class)
            ->between(
                start: $start,
                end: $end,
            )
            ->$per()
            ->count();

        return [
            'datasets' => [
                [
                    'label' => 'Tickets',
                    'data' => $data->map(fn (TrendValue $value) => $value->aggregate),
                ],
            ],
            'labels' => $data->map(fn (TrendValue $value) => $value->date),         
        ];
    }

    protected function getFilters(): ?array
    {
        return[
            'today' => 'Today',
            'week' => 'This Week',
            'month' => 'This Month',
            'year' => 'This Year',
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
