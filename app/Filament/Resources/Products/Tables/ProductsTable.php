<?php

namespace App\Filament\Resources\Products\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Table;
use Filament\Tables\Columns\BooleanColumn;
use Filament\Tables\Columns\TextColumn;

class ProductsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                //
                TextColumn::make('name')
                    ->label('Product Name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('sku')
                    ->label('SKU')
                    ->searchable()
                    ->sortable(),
                    
                TextColumn::make('category.name')
                    ->label('Category')
                    ->searchable()
                    ->sortable(),
                
                TextColumn::make('stock')
                    ->label('Stock')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('Default Unit')
                    ->label('Default Unit')
                    ->getStateUsing(function ($record) {
                        $baseUnit = $record->units()->where('is_base', true)->first();
                        return $baseUnit ? $baseUnit->unit->name : '-';
                    })
                    ->sortable(),
                // cost price
                TextColumn::make('Cost Price')
                    ->label('Cost Price')
                    ->getStateUsing(function ($record) {
                        $baseUnit = $record->units()->where('is_base', true)->first();
                        return $baseUnit ? number_format($baseUnit->cost_price, 2) : '-';
                    })
                    ->sortable(),
                // sell price
                TextColumn::make('Sell Price')
                    ->label('Sell Price')
                    ->getStateUsing(function ($record) {
                        $baseUnit = $record->units()->where('is_base', true)->first();
                        return $baseUnit ? number_format($baseUnit->sell_price, 2) : '-';
                    })
                    ->sortable(),

                
                
                BooleanColumn::make('is_active')
                    ->label('Active')
                    ->sortable(),

            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->groups([
                'parent.name'
            ]);
            // ->groups([
            //     Group::make('parent_id')
            //         ->getKeyFromRecordUsing(fn (Post $record): string => $record->parent_id),
            // ]);
    }
}
