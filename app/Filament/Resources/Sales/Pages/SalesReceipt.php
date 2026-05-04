<?php

namespace App\Filament\Resources\Sales\Pages;

use App\Filament\Resources\Sales\SaleResource;
use Filament\Resources\Pages\Page;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;

class SalesReceipt extends Page
{
    use InteractsWithRecord;
    protected static string $resource = SaleResource::class;

    protected string $view = 'filament.resources.sales.pages.sales-receipt';

    
    public function mount(int | string $record): void
    {
        $this->record = $this->resolveRecord($record);
    }

    
}
