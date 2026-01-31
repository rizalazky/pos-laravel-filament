<?php

namespace App\Filament\Resources\Purchases\Pages;

use App\Filament\Resources\Purchases\PurchaseResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use App\Services\PurchaseService;
use Illuminate\Database\Eloquent\Model;

class EditPurchase extends EditRecord
{
    protected static string $resource = PurchaseResource::class;

    protected function handleRecordUpdate($record, array $data): Model
    {
        return app(PurchaseService::class)->update($record, $data);
    }

    protected function handleRecordDeletion($record): void
    {
        // dd('deleting', $record);
        app(PurchaseService::class)->delete($record);
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['items'] = $this->record
            ->items
            ->map(function ($item) {
                return [
                    'product_id' => $item->product_id,
                    'unit_id'    => $item->unit_id,
                    'quantity'   => $item->quantity,
                    'price'      => $item->price,
                ];
            })
            ->toArray();

        return $data;
    }



    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->action(function(){
                    $record = $this->getRecord();
                    $this->handleRecordDeletion($record);
                    $this->redirect($this->getResource()::getUrl('index'));
                }),
        ];
    }
}
