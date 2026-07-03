<?php

namespace App\Filament\Resources\Products\RelationManagers;

use App\Filament\Resources\Products\ProductResource;
use App\Filament\Resources\Purchases\PurchaseResource;
use Filament\Actions\CreateAction;
use Filament\Actions\AttachAction;
use Filament\Actions\EditAction;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DetachAction;
use Filament\Actions\DetachBulkAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;
use Filament\Tables;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\TextInput;

class ProductSupplierRelationManager extends RelationManager
{
    protected static string $relationship = 'suppliers';
 

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Supplier Name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('phone_number')
                    ->label('Phone Number')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('pivot.supplier_sku')
                    ->label('Supplier SKU'),

                Tables\Columns\TextColumn::make('pivot.last_purchase_price')
                    ->label('Last Purchase Price')
                    ->money('idr', true),

                Tables\Columns\IconColumn::make('pivot.is_default')
                    ->label('Default Supplier')
                    ->boolean()
                    ->sortable(),
            ])
            ->headerActions([
                 AttachAction::make()
                    ->hiddenLabel()
                    ->icon('heroicon-o-plus')
                    ->color('primary')
                    ->size('md')
                    ->recordTitleAttribute('name')
                    ->schema(fn (AttachAction $action): array => [
                        $action->getRecordSelect(),
                        TextInput::make('pivot.supplier_sku')
                            ->label('Supplier SKU'),
                        Toggle::make('pivot.is_default')
                            ->default(false)
                            ->label('Set as Default Supplier'),
                    ])
                    ->recordSelectSearchColumns(['name', 'phone_number'])
                    ->recordSelectOptionsQuery(fn ($query) => $query)
                    ->preloadRecordSelect(),
            ])
            ->recordActions([
                Action::make('create_purchase')
                    ->hiddenLabel()
                    ->icon('heroicon-o-shopping-cart')
                    ->color('success')
                    ->size('md')
                    ->tooltip(fn ($record) => "Create Purchase for Supplier: {$record->name}")
                    ->url(fn ($record) => PurchaseResource::getUrl('create', [
                        'supplier_id' => $record->id,
                        'product_id' => $this->getOwnerRecord()->id,
                    ]))
                    ->icon('heroicon-o-shopping-cart')
                    ->color('success'),
                EditAction::make('edit')
                    ->hiddenLabel()
                    ->icon('heroicon-o-pencil')
                    ->color('primary')
                    ->size('md')
                    ->tooltip(fn ($record) => "Edit Supplier: {$record->name}")
                    ->form([
                        TextInput::make('supplier_sku')
                            ->label('Supplier SKU'),
                        Toggle::make('is_default')
                            ->default(fn ($record) => $record->pivot->is_default)
                            ->label('Set as Default Supplier'),
                    ])
                    ->action(function ($record, array $data) {
                        $isDefault = $data['is_default'] ?? false;
                        if($isDefault){
                            // Set all other suppliers for this product to not default
                            $this->getOwnerRecord()->suppliers()->updateExistingPivot(
                                $this->getOwnerRecord()->suppliers()->pluck('supplier_id')->toArray(),
                                ['is_default' => false]
                            );
                        }
                        $record->pivot->update($data);
                    }),
                DetachAction::make()->hiddenLabel()
                    ->icon('heroicon-o-trash')
                    ->size('md')
                    ->color('danger'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([

                    DetachBulkAction::make(),
                ]),
            ]);
    }
}
