<?php

namespace App\Filament\Resources\StockOpnames\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextInputColumn;

class ItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'items';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('product_id')
                    ->relationship('product', 'name')
                    ->disabled(),

                TextInput::make('system_stock')
                    ->numeric()
                    ->disabled(),

                TextInput::make('physical_stock')
                    ->numeric()
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                        $system = $get('system_stock') ?? 0;
                        $set('difference', $state - $system);
                    }),

                TextInput::make('difference')
                    ->numeric()
                    ->disabled()
                    ->saved(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('stockopname items')
            ->columns([
                TextColumn::make('product.name')->label('Product'),
                TextColumn::make('system_stock'),
                TextInputColumn::make('physical_stock')
                    ->inputMode('numeric'),

                TextColumn::make('difference')
                    ->state(fn ($record) => $record->physical_stock - $record->system_stock)
                    ->color(fn ($state) =>
                        $state < 0 ? 'danger' :
                        ($state > 0 ? 'success' : 'gray')
                    ),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                // EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
