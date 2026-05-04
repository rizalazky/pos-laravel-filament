<?php

namespace App\Filament\Widgets;

use Filament\Actions\BulkActionGroup;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Product;
use Filament\Tables\Columns\TextColumn;

class LowStockProduct extends TableWidget
{
    protected static ?int $sort = 3;
    protected int | string | array $columnSpan = 'full';
    public function table(Table $table): Table
    {
        return $table
            ->query(fn (): Builder => Product::query()->where('stock', '<=', 10)->orderBy('stock', 'asc'))
            ->columns([
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
                    ->badge()
                    ->formatStateUsing(function ($state, $record) {
                        $baseUnit = $record->units()->where('is_base', true)->first();
                        $formatted = fmod($state, 1) == 0
                            ? number_format($state, 0)
                            : number_format($state, 2);

                        return $baseUnit 
                            ? $formatted . ' ' . $baseUnit->unit->name 
                            : '-';
                    })
                    ->color(fn ($state) => $state <= 5 ? 'danger' : 'warning')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                //
            ])
            ->recordActions([
                //
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    //
                ]),
            ]);
    }
}
