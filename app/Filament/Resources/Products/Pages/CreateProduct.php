<?php

namespace App\Filament\Resources\Products\Pages;

use App\Filament\Resources\Products\ProductResource;
use Filament\Resources\Pages\CreateRecord;

class CreateProduct extends CreateRecord
{
    protected array $unitPayload = [];
    protected static string $resource = ProductResource::class;
    // protected function mutateFormDataBeforeCreate(array $data): array
    // {
    //     // if (request()->has('parent_id')) {
    //     //     $data['parent_id'] = request('parent_id');
    //     // }
    //     dd($data);

    //     return $data;
    // }

    // protected function afterCreate(): void
    // {
    //     $product = $this->record;

    //     // dd($product);

    //     // $variant = $product->variants()->create([
    //     //     'variant_name' => 'Default',
    //     //     'is_default' => true,
    //     // ]);

    //     $product->units()->create([
    //         'unit_id' => $this->unitPayload['unit_id'],
    //         'is_base' => true,
    //         'conversion_rate' => 1,
    //         'cost_price' => $this->unitPayload['cost_price'],
    //         'sell_price' => $this->unitPayload['sell_price'],
    //         'stock' => 0,
    //     ]);
    // }

}
