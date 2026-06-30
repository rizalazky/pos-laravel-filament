<?php

namespace App\Filament\Resources\StockMovements\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class StockMovementForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('product_id')
                    ->required()
                    ->numeric(),
                TextInput::make('input_unit_id')
                    ->required()
                    ->numeric(),
                TextInput::make('input_quantity')
                    ->required()
                    ->numeric(),
                TextInput::make('conversion_rate')
                    ->required()
                    ->numeric(),
                TextInput::make('base_quantity')
                    ->required()
                    ->numeric(),
                Select::make('type')
                    ->options(['in' => 'In', 'out' => 'Out', 'adjust' => 'Adjust'])
                    ->required(),
                TextInput::make('stock_before')
                    ->required()
                    ->numeric(),
                TextInput::make('stock_after')
                    ->required()
                    ->numeric(),
                TextInput::make('reference_type'),
                TextInput::make('reference_id')
                    ->numeric(),
                Textarea::make('note')
                    ->columnSpanFull(),
                TextInput::make('created_by')
                    ->numeric(),
            ]);
    }
}
