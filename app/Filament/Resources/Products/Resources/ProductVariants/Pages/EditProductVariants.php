<?php

namespace App\Filament\Resources\Products\Resources\ProductVariants\Pages;

use App\Filament\Resources\Products\Resources\ProductVariants\ProductVariantsResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditProductVariants extends EditRecord
{
    protected static string $resource = ProductVariantsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
