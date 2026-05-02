<?php

namespace App\Filament\Resources\StockOpnames\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\Action;
use App\Services\StockService;
use Filament\Notifications\Notification;
use Filament\Actions\DeleteAction;

class StockOpnamesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')->label('#'),
                TextColumn::make('date')->date()->sortable(),
                TextColumn::make('note')->label('Note'),
                TextColumn::make('status')->label('Status'),
            ])
            ->defaultSort('date', direction: 'desc')
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
                Action::make('submit')
                    ->label('Submit')
                    ->color('success')
                    ->visible(fn ($record) => $record->status === 'draft')
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        
                        \DB::transaction(function () use ($record) {

                            foreach ($record->items as $item) {

                                $product = \App\Models\Product::find($item->product_id);

                                
                                $difference = $item->physical_stock - $item->system_stock;
                                

                                if ($difference == 0) {
                                    continue;
                                }

                                // kita anggap pakai base unit
                                $unitId = $product->baseUnit->unit_id;

                                
                                app(StockService::class)->adjust(
                                    $product,
                                    $unitId,
                                    $difference,
                                    'stock_opname',
                                    $record->id,
                                    'Stock Opname: ' . $record->code
                                );
                            }

                            $record->update([
                                'status' => 'completed'
                            ]);
                        });
                    })->after(function () {
                        Notification::make()
                            ->title('Stock Opname berhasil disubmit')
                            ->success()
                            ->send();
                    }),
                DeleteAction::make()
                    ->requiresConfirmation()
                    ->action(function ($record) {

                        $stockService = app(StockService::class);

                        \DB::transaction(function () use ($record, $stockService) {

                            // kalau sudah completed → rollback dulu
                            if ($record->status === 'completed') {

                                $stockService->rollbackByReference(
                                    'stock_opname',
                                    $record->id
                                );
                            }

                            // baru delete opname
                            $record->delete();
                        });

                        Notification::make()
                            ->title('Stock Opname berhasil dihapus & rollback')
                            ->success()
                            ->send();
                    })
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
