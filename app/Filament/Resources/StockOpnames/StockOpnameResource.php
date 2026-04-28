<?php

namespace App\Filament\Resources\StockOpnames;

use App\Filament\Resources\StockOpnames\Pages\CreateStockOpname;
use App\Filament\Resources\StockOpnames\Pages\EditStockOpname;
use App\Filament\Resources\StockOpnames\Pages\ListStockOpnames;
use App\Filament\Resources\StockOpnames\Schemas\StockOpnameForm;
use App\Filament\Resources\StockOpnames\Tables\StockOpnamesTable;
use App\Models\StockOpname;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class StockOpnameResource extends Resource
{
    protected static ?string $model = StockOpname::class;
    protected static string|UnitEnum|null $navigationGroup = 'Inventory Management';
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'Stock Opname';
    protected static ?string $navigationLabel = 'Stock Opname';

    public static function form(Schema $schema): Schema
    {
        return StockOpnameForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return StockOpnamesTable::configure($table);
        // Tables\Actions\Action::make('submit')
        //         ->label('Submit')
        //         ->color('success')
        //         ->visible(fn ($record) => $record->status === 'draft')
        //         ->requiresConfirmation()
        //         ->action(function ($record) {

        //             \DB::transaction(function () use ($record) {

        //                 foreach ($record->items as $item) {

        //                     $product = \App\Models\Product::find($item->product_id);

        //                     $stockBefore = $product->stock;

        //                     $product->stock = $item->physical_stock;
        //                     $product->save();

        //                     if ($item->difference != 0) {
        //                         \App\Models\StockMovement::create([
        //                             'product_id' => $product->id,
        //                             'type' => $item->difference > 0 ? 'in' : 'out',
        //                             'reference' => $record->code,
        //                             'qty' => abs($item->difference),
        //                             'stock_before' => $stockBefore,
        //                             'stock_after' => $product->stock,
        //                             'note' => 'Stock Opname Adjustment'
        //                         ]);
        //                     }
        //                 }

        //                 $record->update([
        //                     'status' => 'completed'
        //                 ]);
        //             });

        //         });
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\ItemsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListStockOpnames::route('/'),
            'create' => CreateStockOpname::route('/create'),
            'edit' => EditStockOpname::route('/{record}/edit'),
        ];
    }
}
