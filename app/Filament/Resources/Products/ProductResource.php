<?php

namespace App\Filament\Resources\Products;

use App\Filament\Resources\Products\Pages\CreateProduct;
use App\Filament\Resources\Products\Pages\EditProduct;
use App\Filament\Resources\Products\Pages\ListProducts;
use App\Filament\Resources\Products\Schemas\ProductForm;
use App\Filament\Resources\Products\Tables\ProductsTable;
use App\Models\Product;
use BackedEnum;
use UnitEnum;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use App\Models\Unit;
use Filament\Support\RawJs;
use Filament\Schemas\Components\Tabs;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Repeater\TableColumn;
use Filament\Forms\Components\Toggle;


class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static string|UnitEnum|null $navigationGroup = 'Catalog';
    protected static ?int $navigationSort = 3;

    protected static string|BackedEnum|null $navigationIcon ="heroicon-o-archive-box";

    protected static ?string $recordTitleAttribute = 'product';


    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Tabs::make('Product Tabs')->tabs([

                // TAB 1
                Tabs\Tab::make('Product Info')
                    ->schema(self::productInfoSchema())
                    ->columns(2),

                // TAB 2
                Tabs\Tab::make('Units & Price')
                    ->schema(self::unitSchema()),
            ]),
        ])->columns(1);
    }

    protected static function productInfoSchema(): array
    {
        // dd(request()->get('parent_id'));
        $parentProduct = null;
        if(request()->has('parent_id')){
            $parentProduct = Product::find(request()->get('parent_id'));
        }

        return [
            TextInput::make('name')
                ->required()
                ->maxLength(255),

            Select::make('parent_id')
                ->relationship('parent', 'name')
                ->hidden()
                ->default(request()->get('parent_id')),
                // ->disabled()
                // ->visible(fn () => request()->has('parent_id')),

            Select::make('category_id')
                ->label('Category')
                ->relationship('category', 'name')
                ->default($parentProduct?->category_id)
                ->required(),

            TextInput::make('sku')
                ->unique(ignoreRecord: true)
                ->maxLength(100),
        ];
    }

    protected static function unitSchema(): array
    {
        return [
            Repeater::make('units')
                ->hiddenLabel()
                ->relationship()
                ->schema([
                    TableColumn::make('Unit'),
                    TableColumn::make('Conversion Rate'),
                    TableColumn::make('Cost Price'),
                    TableColumn::make('Sell Price'),
                    TableColumn::make('Is Base Unit'),
                ])
                ->schema([
                    Select::make('unit_id')
                        ->relationship('unit', 'name')
                        ->required(),

                    TextInput::make('conversion_rate')
                        ->numeric()
                        ->hidden()
                        ->default(1)
                        ->required(),

                    TextInput::make('cost_price')
                        ->mask(RawJs::make('$money($input)'))
                        ->stripCharacters(',')
                        ->numeric()
                        ->required(),

                    TextInput::make('sell_price')
                        ->mask(RawJs::make('$money($input)'))
                        ->stripCharacters(',')
                        ->numeric()
                        ->required(),

                    Toggle::make('is_base')
                        // ->disabled()
                        ->hidden()
                        ->default(true), // true on create, false on edit
                ])
                ->minItems(1)
                ->maxItems(1)
                ->addable(false)
                ->deletable(false)
                ->required(),
        ];
    }

    


    public static function table(Table $table): Table
    {
        return ProductsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
            'variants' => RelationManagers\VariantsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListProducts::route('/'),
            'create' => CreateProduct::route('/create'),
            'edit' => EditProduct::route('/{record}/edit'),
            'barcode' => Pages\ProductBarcode::route('/barcode'),
        ];
    }
}
