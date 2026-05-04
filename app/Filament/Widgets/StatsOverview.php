<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Sale;
use App\Models\Product;
use Carbon\Carbon;
use Filament\Widgets\Concerns\InteractsWithPageFilters;

class StatsOverview extends StatsOverviewWidget
{
    use InteractsWithPageFilters;
    protected function getStats(): array
    {
        $startDate = $this->pageFilters['startDate'] ?? Carbon::today();
        $endDate = $this->pageFilters['endDate'] ?? Carbon::today();

        $totalSales = Sale::whereBetween('date', [$startDate, $endDate])->sum('total');
        $totalTransactions = Sale::whereBetween('date', [$startDate, $endDate])->count();

        $averageSales = $totalTransactions > 0 
            ? $totalSales / $totalTransactions 
            : 0;

        return [
            Stat::make('Total Sales', 'Rp ' . number_format($totalSales)),

            Stat::make('Transaction Count', $totalTransactions),

            Stat::make('Average Sales', 'Rp ' . number_format($averageSales)),
        ];
    }
}
