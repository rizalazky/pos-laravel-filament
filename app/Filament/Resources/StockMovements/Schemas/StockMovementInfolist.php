<?php

namespace App\Filament\Resources\StockMovements\Schemas;

use Filament\Schemas\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class StockMovementInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                Section::make('Product Information')
                    ->columns(2)
                    ->schema([

                        TextEntry::make('product.name')
                            ->label('Product')
                            ->weight('bold')
                            ->url(fn ($record) => \App\Filament\Resources\Products\ProductResource::getUrl('edit', ['record' => $record->product_id]))
                            ->color('primary')
                            ->openUrlInNewTab(),

                        TextEntry::make('type')
                            ->badge()
                            ->colors([
                                'success' => 'in',
                                'danger' => 'out',
                                'warning' => 'adjustment',
                            ])
                            ->formatStateUsing(fn (string $state) => match ($state) {
                                'in' => 'Stock In',
                                'out' => 'Stock Out',
                                'adjustment' => 'Adjustment',
                                default => ucfirst($state),
                            }),

                        TextEntry::make('reference_number')
                            ->label('Reference')
                            ->state(fn ($record) => match ($record->reference_type) {
                                'stock_opname' => $record->reference?->code,
                                'purchase', 'sale' => $record->reference?->invoice_number,
                                default => '-',
                            })
                            ->url(fn ($record) => match ($record->reference_type) {
                                'stock_opname' => \App\Filament\Resources\StockOpnames\StockOpnameResource::getUrl('edit', ['record' => $record->reference]),
                                'purchase' => \App\Filament\Resources\Purchases\PurchaseResource::getUrl('edit', ['record' => $record->reference]),
                                'sale' => \App\Filament\Resources\Sales\SaleResource::getUrl('edit', ['record' => $record->reference]),
                                default => null,
                            })
                            ->color('primary')
                            ->openUrlInNewTab(),

                        TextEntry::make('creator.name')
                            ->label('Created By')
                            ->placeholder('-'),

                        TextEntry::make('created_at')
                            ->label('Created At')
                            ->dateTime('d M Y H:i'),

                    ]),

                Section::make('Movement')
                    ->columns(3)
                    ->schema([
                        TextEntry::make('stock_before')
                            ->label('Stock Before')
                            ->formatStateUsing(fn ($record) =>
                                rtrim(rtrim(number_format($record->stock_before, 2), '0'), '.')
                                .' '.$record->product?->baseUnit?->unit?->name
                            ),
                        
                        TextEntry::make('base_quantity')
                            ->label('Quantity')
                            ->formatStateUsing(fn ($record) =>
                                rtrim(rtrim(number_format($record->base_quantity, 2), '0'), '.')
                                .' '.$record->product?->baseUnit?->unit?->name
                            ),

                        TextEntry::make('stock_after')
                            ->label('Stock After')
                            ->formatStateUsing(fn ($record) =>
                                rtrim(rtrim(number_format($record->stock_after, 2), '0'), '.')
                                .' '.$record->product?->baseUnit?->unit?->name
                            ),

                    ]),
            ]);
    }
}