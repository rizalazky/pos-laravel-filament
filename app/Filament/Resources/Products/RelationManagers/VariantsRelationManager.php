<?php

namespace App\Filament\Resources\Products\RelationManagers;

use App\Filament\Resources\Products\ProductResource;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use App\Models\Unit;
use Filament\Support\RawJs;
use App\Models\Product;;
use Illuminate\Database\Eloquent\Model;

class VariantsRelationManager extends RelationManager
{
    protected static string $relationship = 'variants';

    protected static ?string $recordTitleAttribute = 'name';
    public function table(Table $table): Table
    {
        return $table
            // table title
            ->heading('Variants')
            ->columns([
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
            ->headerActions([
                Action::make('addVariant')
                    ->label('Add Variant')
                    ->url(fn () => ProductResource::getUrl('create', [
                        'parent_id' => $this->getOwnerRecord(),
                    ])),
            ])
            ->recordActions([
                Action::make('edit')
                ->url(fn ($record) =>
                    ProductResource::getUrl('edit', ['record' => $record])
                ),
                    DeleteAction::make()->modalHeading('Delete Variant'),
            ]);
    }

    public static function canViewForRecord(
        Model $ownerRecord,
        string $pageClass
    ): bool {
        return is_null($ownerRecord->parent_id);
    }

    // public function form(Schema $schema): Schema
    // {
    //     return $schema->schema([
    //         TextInput::make('variant_name')
    //             ->label('Variant Name')
    //             ->disabled(fn ($record) => $record != null && $record->variant_name === 'Default') // disable editing variant name if named 'Default'
    //             ->required(),

    //         Select::make('base_unit_id')
    //             ->disabledOn('edit')
    //             ->label('Base Unit')
    //             ->options(
    //                 Unit::query()
    //                     ->pluck('name', 'id')
    //             )
    //             ->searchable()
    //             ->required(),

    //         TextInput::make('cost_price')
    //             ->label('Cost Price')
    //             ->mask(RawJs::make('$money($input)'))
    //             ->stripCharacters(',')
    //             ->numeric()
    //             ->default(0)
    //             ->required(),

    //         TextInput::make('sell_price')
    //             ->label('Sell Price')
    //             ->mask(RawJs::make('$money($input)'))
    //             ->stripCharacters(',')
    //             ->numeric()
    //             ->default(0)
    //             ->required(),
    //     ]);
    // }
}
