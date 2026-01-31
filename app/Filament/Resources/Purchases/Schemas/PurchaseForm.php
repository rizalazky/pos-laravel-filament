<?php

namespace App\Filament\Resources\Purchases\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater\TableColumn;
use Filament\Support\RawJs;
use App\Models\Product;
use App\Models\Unit;
use App\Models\ProductUnit;
use Filament\Schemas\Components\Utilities\Get;

class PurchaseForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                //
                DatePicker::make('date')
                ->required()
                ->default(now()),

                TextInput::make('invoice_number')
                    ->required()
                    ->unique(ignoreRecord: true),

                Textarea::make('note')
                    ->columnSpanFull(),

                Repeater::make('items')
                    ->table([
                        TableColumn::make('Product'),
                        TableColumn::make('Unit'),
                        TableColumn::make('Quantity'),
                        TableColumn::make('Price'),
                        TableColumn::make('Subtotal'),
                        TableColumn::make('Actions'),
                    ])
                    // ->relationship()
                    ->schema([
                        Select::make('product_id')
                            ->options(Product::pluck('name', 'id'))
                            ->searchable()
                            ->reactive()
                            ->afterStateUpdated(fn ($set) => $set('unit_id', null))
                            ->required(),

                        Select::make('unit_id')
                            ->options(function (Get $get) {
                                $productId = $get('product_id');

                                if (! $productId) {
                                    return [];
                                }

                                return ProductUnit::where('product_id', $productId)
                                    ->with('unit')
                                    ->get()
                                    ->pluck('unit.name', 'unit.id')
                                    ->toArray();
                            })
                            ->disabled(fn (Get $get) => ! $get('product_id'))
                            ->required(),

                        TextInput::make('quantity')
                            ->numeric()
                            ->reactive()
                            ->required(),

                        TextInput::make('price')
                            // ->mask(RawJs::make('$money($input)'))
                            ->numeric()
                            ->reactive()
                            ->required(),

                        Placeholder::make('subtotal') // currency format
                            ->content(fn ($get) =>
                                ($get('quantity') ?? 0) * ($get('price') ?? 0)
                            ),
                    ])
                    ->columns(5)
                    ->columnSpanFull()
                    ->required(),
            ]);
    }
}
