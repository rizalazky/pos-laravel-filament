<?php

namespace App\Filament\Resources\Sales\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;


class SalesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                //
                TextColumn::make('invoice_number')->label('Invoice #')->searchable()->sortable(),
                // TextColumn::make('customer.name')->label('Customer')->searchable()->sortable(),
                TextColumn::make('date')->date()->sortable(),
                TextColumn::make('total')->money('idr', true)->sortable(),
                TextColumn::make('note')->sortable(),
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
            ]);
    }
}
