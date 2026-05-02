<?php

namespace App\Filament\Resources\Products\Pages;

use App\Filament\Resources\Products\ProductResource;
use Filament\Resources\Pages\Page;
use App\Models\Product;


class ProductBarcode extends Page
{
    protected static string $resource = ProductResource::class;

    protected string $view = 'filament.resources.products.pages.product-barcode';
    public $products;

    public function mount(): void
    {
        $this->products = Product::with('baseUnit')
            // ->whereNotNull('parent_id') // hanya variant
            ->get();
    }
}
