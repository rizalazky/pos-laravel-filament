<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use UnitEnum;
use BackedEnum;
use App\Models\Sale;
use App\Models\SaleItem;
use Filament\Pages\Dashboard\Actions\FilterAction;
use Filament\Pages\Dashboard\Concerns\HasFiltersAction;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Forms\Components\DatePicker;

class SalesReport extends Page
{
    protected string $view = 'filament.pages.sales-report';
    protected static ?string $title = 'POS';
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-chart-bar';
    protected static ?string $navigationLabel = 'Sales Report';
    protected static string|UnitEnum|null $navigationGroup = 'Reports';
    

    public $startDate;
    public $endDate;

    use HasFiltersAction;
    
    protected function getHeaderActions(): array
    {
        return [
            FilterAction::make()
                ->schema([
                    DatePicker::make('startDate')
                        ->default(now()->startOfMonth()),

                    DatePicker::make('endDate')
                        ->default(now()),
                ])
                ->action(function (array $data) {
                    $this->startDate = $data['startDate'];
                    $this->endDate = $data['endDate'];
                }),
        ];
    }

    // protected function getHeaderWidgets(): array
    // {
    //     return [
    //        \App\Filament\Widgets\DailySalesChart::class,
    //     ];
    // }

    protected function getWidgets(): array
    {
        return [
            \App\Filament\Widgets\DailySalesChart::class,
        ];
    }

    public function mount()
    {
        $this->startDate = now()->startOfMonth()->toDateString();
        $this->endDate = now()->toDateString();
    }

    public function getTotalSalesProperty()
    {
        return Sale::whereBetween('date', [$this->startDate, $this->endDate])
            ->sum('total');
    }

    public function getTotalTransactionsProperty()
    {
        return Sale::whereBetween('date', [$this->startDate, $this->endDate])
            ->count();
    }

    public function getTotalQuantityProperty()
    {
        return SaleItem::whereHas('sale', function ($q) {
                $q->whereBetween('date', [$this->startDate, $this->endDate]);
            })
            ->sum('base_quantity');
    }

    public function getDailySalesProperty()
    {
        return Sale::selectRaw('date, SUM(total) as total')
            ->whereBetween('date', [$this->startDate, $this->endDate])
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('total', 'date')
            ->toArray();
    }

    public function getTopProductsProperty()
    {
        return SaleItem::selectRaw('product_id, SUM(base_quantity) as total_qty, SUM(subtotal) as total_revenue')
            ->whereHas('sale', function ($q) {
                $q->whereBetween('date', [$this->startDate, $this->endDate]);
            })
            ->groupBy('product_id')
            ->with('product')
            ->orderByDesc('total_qty')
            ->limit(5)
            ->get();
    }

    public function getSalesByCashierProperty()
    {
        return Sale::selectRaw('created_by, COUNT(*) as total_transaction, SUM(total) as total_sales')
            ->whereBetween('date', [$this->startDate, $this->endDate])
            ->groupBy('created_by')
            ->with('user')
            ->get();
    }
}
