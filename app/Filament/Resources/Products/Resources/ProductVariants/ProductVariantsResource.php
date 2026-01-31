<?php

namespace App\Filament\Resources\Products\Resources\ProductVariants;

use App\Filament\Resources\Products\ProductResource;
use App\Filament\Resources\Products\Resources\ProductVariants\Pages\CreateProductVariants;
use App\Filament\Resources\Products\Resources\ProductVariants\Pages\EditProductVariants;
use App\Filament\Resources\Products\Resources\ProductVariants\Schemas\ProductVariantsForm;
use App\Filament\Resources\Products\Resources\ProductVariants\Tables\ProductVariantsTable;
use App\Models\Product;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ProductVariantsResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $parentResource = ProductResource::class;

    protected static ?string $recordTitleAttribute = 'productvariants';

    public static function form(Schema $schema): Schema
    {
        return ProductVariantsForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ProductVariantsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'create' => CreateProductVariants::route('/create'),
            'edit' => EditProductVariants::route('/{record}/edit'),
        ];
    }
}
