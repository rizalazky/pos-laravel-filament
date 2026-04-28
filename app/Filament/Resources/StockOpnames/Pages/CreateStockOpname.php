<?php

namespace App\Filament\Resources\StockOpnames\Pages;

use App\Filament\Resources\StockOpnames\StockOpnameResource;
use Filament\Resources\Pages\CreateRecord;
use App\Models\Product;

class CreateStockOpname extends CreateRecord
{
    protected static string $resource = StockOpnameResource::class;
    protected function afterCreate(): void
    {
        $products = Product::all();

        foreach ($products as $product) {
            $this->record->items()->create([
                'product_id' => $product->id,
                'system_stock' => $product->stock,
                'physical_stock' => 0,
                'difference' => 0,
            ]);
        }
    }
}
