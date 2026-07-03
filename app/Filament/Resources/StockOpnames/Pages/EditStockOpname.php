<?php

namespace App\Filament\Resources\StockOpnames\Pages;

use App\Filament\Resources\StockOpnames\StockOpnameResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Actions\Action;
use App\Services\StockService;
use Filament\Notifications\Notification;

class EditStockOpname extends EditRecord
{
    protected static string $resource = StockOpnameResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('submit')
                    ->label('Submit')
                    ->color('success')
                    ->visible(fn ($record) => $record->status === 'draft' && $record->items()->count() > 0)
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
            DeleteAction::make(),
        ];
    }
}
