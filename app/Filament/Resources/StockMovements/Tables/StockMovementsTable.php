<?php

namespace App\Filament\Resources\StockMovements\Tables;

use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class StockMovementsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([

                TextColumn::make('created_at')
                    ->label('Date')
                    ->dateTime('d M Y H:i')
                    ->sortable(),

                TextColumn::make('product.name')
                    ->label('Product')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),


                TextColumn::make('base_quantity')
                    ->label('Quantity')
                    ->formatStateUsing(fn ($record) =>
                        rtrim(rtrim(number_format($record->base_quantity, 2), '0'), '.')
                        . ' ' . $record->product?->baseUnit?->unit->name
                    )
                    ->alignEnd(),

                TextColumn::make('stock_before')
                    ->label('Before')
                    ->formatStateUsing(fn ($record) =>
                        rtrim(rtrim(number_format($record->stock_before, 2), '0'), '.')
                        . ' ' . $record->product?->baseUnit?->unit->name
                    )
                    ->alignEnd()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('stock_after')
                    ->label('After')
                    ->formatStateUsing(fn ($record) =>
                        rtrim(rtrim(number_format($record->stock_after, 2), '0'), '.')
                        . ' ' . $record->product?->baseUnit?->unit->name
                    )
                    ->alignEnd()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('type')
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


                TextColumn::make('reference_number')
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

                TextColumn::make('creator.name')
                    ->label('Created By')
                    ->toggleable(),

            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
            ]);
    }
}