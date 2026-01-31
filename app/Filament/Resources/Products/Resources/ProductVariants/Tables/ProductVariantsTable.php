<?php

namespace App\Filament\Resources\Products\Resources\ProductVariants\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Actions\CreateAction;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;

use App\Models\Product;

class ProductVariantsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                //
                TextColumn::make('name')->label('Variant Name'),
                TextColumn::make('Base Unit')->getStateUsing(function ($record) {
                    $baseUnit = $record->units()->where('is_base', true)->first();
                    return $baseUnit ? $baseUnit->unit->name : '-';
                }),
                TextColumn::make('cost_price')->getStateUsing(function ($record) {
                    $baseUnit = $record->units()->where('is_base', true)->first();
                    return $baseUnit ? number_format($baseUnit->cost_price, 2) : '-';
                })->label('Cost Price'),
                TextColumn::make('sell_price')->getStateUsing(function ($record) {
                    $baseUnit = $record->units()->where('is_base', true)->first();
                    return $baseUnit ? number_format($baseUnit->sell_price, 2) : '-';
                })->label('Sell Price'),
                IconColumn::make('is_active')->boolean(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                //
                Action::make('add')
                    ->label('Add Variant')
                    ->url(fn (Product $record): string => route('product.create', ['product_id' => $record->id])),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
