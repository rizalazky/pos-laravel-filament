<?php

namespace App\Filament\Resources\Purchases\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Placeholder;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Repeater\TableColumn;
use Filament\Support\RawJs;
use App\Models\Product;
use App\Models\Unit;
use App\Models\ProductUnit;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
class PurchaseForm
{

    protected static function calculateTotals(Get $get, Set $set): void
    {
        $items = collect($get('items') ?? []);

        $total = $items->sum(function ($item) {
            return ((float) ($item['quantity'] ?? 0))
                * ((float) ($item['price'] ?? 0));
        });

        $discount = (float) ($get('discount') ?? 0);

        $set('total', $total);
        $set('grand_total', max(0, $total - $discount));
    }

    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                //
                DatePicker::make('date')
                    ->required()
                    ->default(now()),

                TextInput::make('invoice_number')
                    ->disabled()
                    ->label('Document Number')
                    ->placeholder('AUTO GENERATED')
                    ->unique(ignoreRecord: true),
                
                Select::make('supplier_id')
                    ->label('Pilih Supplier / Vendor')
                    ->relationship('supplier', 'name')
                    ->live()
                    ->searchable()
                    ->preload(),
                
                TextInput::make('total')
                    ->disabled()
                    ->dehydrated()
                    ->numeric()
                    ->default(0),        

                TextInput::make('discount')
                    ->numeric()
                    ->default(0)
                    ->live()
                    ->afterStateUpdated(fn (Get $get, Set $set)
                        => self::calculateTotals($get, $set)),

                TextInput::make('grand_total')
                    ->disabled()
                    ->dehydrated()
                    ->numeric()
                    ->default(0),
                TextInput::make('total_paid')
                            ->label('Total Payment')
                            ->required()
                            // ->mask(RawJs::make('$money($input)'))
                            ->numeric()
                            ->live(onBlur: false)
                            ->reactive(),

                Textarea::make('note')
                    ->columnSpan(2),

                Section::make('items')
                    ->columnSpanFull()
                    ->schema([
                        Repeater::make('items')
                            ->hiddenLabel()
                            ->table([
                                TableColumn::make('Product'),
                                TableColumn::make('Unit'),
                                TableColumn::make('Quantity'),
                                TableColumn::make('Price'),
                                TableColumn::make('Subtotal'),
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
                                    ->reactive()
                                    ->live()
                                    ->afterStateUpdated(function ($state, Set $set, Get $get) {

                                        if (! $state) {
                                            return;
                                        }

                                        $productUnit = ProductUnit::where('product_id', $get('product_id'))
                                            ->where('unit_id', $state)
                                            ->first();

                                        if (! $productUnit) {
                                            return;
                                        }

                                        $set('price', $productUnit->cost_price);

                                        self::calculateTotals($get, $set);
                                    })
                                    ->required(),
        
                                TextInput::make('quantity')
                                    ->numeric()
                                    ->default(1)
                                    ->live()
                                    ->afterStateUpdated(fn (Get $get, Set $set)
                                        => self::calculateTotals($get, $set))
                                    ->required(),
        
                                TextInput::make('price')
                                    ->numeric()
                                    ->default(0)
                                    ->live()
                                    ->afterStateUpdated(fn (Get $get, Set $set)
                                        => self::calculateTotals($get, $set))
                                    ->required(),
        
                                // TextInput::make('subtotal') // currency format
                                //     ->disabled()
                                //     ->default(fn ($get) =>
                                //         ($get('quantity') ?? 0) * ($get('price') ?? 0)
                                //     )
                                //     ->reactive()
                                //     ->saved(),
                                Placeholder::make('subtotal') // currency format
                                    ->content(fn ($get) =>
                                        ($get('quantity') ?? 0) * ($get('price') ?? 0)
                                    ),
                            ])
                            ->columns(5)
                            ->columnSpanFull()
                            ->live()
                            ->afterStateUpdated(fn (Get $get, Set $set)
                                => self::calculateTotals($get, $set))
                            ->required(),
                        
                    ])
            ])->columns(3);
    }
}
