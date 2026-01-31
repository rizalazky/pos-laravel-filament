<?php

namespace App\Filament\Resources\Products\Pages;

use App\Filament\Resources\Products\ProductResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditProduct extends EditRecord
{
    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    // protected function mutateFormDataBeforeFill(array $data): array
    // {
    //     $product = $this->record;

    //     $unit = $product->units()
    //         ->where('is_base', true)
    //         ->first();

    //     if ($unit) {
    //         $data['base_unit_id'] = $unit->unit_id;
    //         $data['cost_price']  = $unit->cost_price;
    //         $data['sell_price']  = $unit->sell_price;
    //     }

    //     return $data;
    // }
    // protected function mutateFormDataBeforeSave(array $data): array
    // {
    //     $this->unitPayload = [
    //         // 'unit_id'     => $data['base_unit_id'],
    //         'cost_price'  => $data['cost_price'],
    //         'sell_price'  => $data['sell_price'],
    //     ];

    //     unset(
    //         $data['base_unit_id'],
    //         $data['cost_price'],
    //         $data['sell_price'],
    //     );

    //     return $data;
    // }
    // protected array $unitPayload = [];

    // protected function afterSave(): void
    // {
    //     $product = $this->record;

       

    //     $unit = $product->units()
    //         ->where('is_base', true)
    //         ->first();

    //     if ($unit) {
    //         $unit->update($this->unitPayload);
    //     }
    // }


}
