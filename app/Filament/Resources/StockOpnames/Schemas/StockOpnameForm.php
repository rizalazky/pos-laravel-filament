<?php

namespace App\Filament\Resources\StockOpnames\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;

class StockOpnameForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('code')
                    ->default('OP-' . now()->format('YmdHis'))
                    ->disabled()
                    ->dehydrated(),

                DatePicker::make('date')
                    ->default(now())
                    ->required(),

                Textarea::make('note'),

                Select::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'completed' => 'Completed',
                    ])
                    ->default('draft')
                    ->disabled(),
            ]);
    }
}
