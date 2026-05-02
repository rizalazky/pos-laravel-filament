<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Sale;
// use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Forms\Components\DatePicker;
use Filament\Schemas\Schema;
use Filament\Widgets\ChartWidget\Concerns\HasFiltersSchema;
use Filament\Widgets\Concerns\InteractsWithPageFilters;

class DailySalesChart extends ChartWidget
{
    // use InteractsWithPageFilters;
    protected ?string $heading = 'Daily Sales Chart';
    protected int | string | array $columnSpan = 'full';
    use InteractsWithPageFilters;

    protected ?string $maxHeight = '300px';

    
    protected function getData(): array
    {
        $startDate = $this->pageFilters['startDate'] ?? null;
        $endDate = $this->pageFilters['endDate'] ?? null;

        $data = Sale::selectRaw('date, SUM(total) as total')
            ->whereBetween('date', [$startDate, $endDate])
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'Sales',
                    'data' => $data->pluck('total'),
                ],
            ],
            'labels' => $data->pluck('date'),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
