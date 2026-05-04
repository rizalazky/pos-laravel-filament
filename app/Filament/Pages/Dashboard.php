<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Forms\Components\DatePicker;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Pages\Dashboard\Actions\FilterAction;
use Filament\Pages\Dashboard\Concerns\HasFiltersAction;
use Carbon\Carbon;

class Dashboard extends BaseDashboard
{
    
    use HasFiltersAction;
    protected function getHeaderActions(): array
    {
        return [
            FilterAction::make()
                ->schema([
                    DatePicker::make('startDate')->default(Carbon::today()),
                    DatePicker::make('endDate')->default(Carbon::today()),
                    // ...
                ]),
        ];
    }
}