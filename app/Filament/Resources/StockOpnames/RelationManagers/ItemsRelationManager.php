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
use App\Models\Product;
use App\Models\StockOpnameItem;

class ItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'items';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('product_id')
                    ->relationship('product', 'name')
                    ->required()
                    ->searchable()
                    ->getSearchResultsUsing(fn (string $search): array => Product::query()
                        ->where('name', 'like', "%{$search}%")
                        ->orwhere('sku', 'like', "%{$search}%")
                        ->limit(50)
                        ->pluck('name', 'id')
                        ->all()
                    ),

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
                TextColumn::make('product.name')->label('Product')->searchable(),
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
                CreateAction::make()
                    ->label('Add Item')
                    ->form([
                        Select::make('product_id')
                            ->label('Product')
                            ->relationship('product', 'name')
                            ->searchable()
                            ->searchPrompt('Search products by their name or SKU')
                            ->getSearchResultsUsing(fn (string $search): array => Product::query()
                                ->where('name', 'like', "%{$search}%")
                                ->orwhere('sku', 'like', "%{$search}%")
                                ->limit(50)
                                ->pluck('name', 'id')
                                ->all()
                            )
                            ->preload()
                            ->required()
                            ->rules([
                                function () {
                                    return function ($attribute, $value, $fail) {
                                        $exists = StockOpnameItem::query()
                                            ->where('stock_opname_id', $this->getOwnerRecord()->id)
                                            ->where('product_id', $value)
                                            ->exists();

                                        if ($exists) {
                                            $fail('Product already exists in this stock opname.');
                                        }
                                    };
                                },
                            ]),
                    ])
                    ->mutateDataUsing(function (array $data) {

                        $product = Product::findOrFail($data['product_id']);

                        return [
                            'product_id'     => $product->id,
                            'system_stock'   => $product->stock,
                            'physical_stock' => $product->stock,
                            'difference'     => 0,
                        ];
                    }),
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
