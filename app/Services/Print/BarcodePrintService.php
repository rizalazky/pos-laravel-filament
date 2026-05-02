<?php

namespace App\Services\Print;

use App\Models\Product;
use App\Services\BarcodeService;
use Barryvdh\DomPDF\Facade\Pdf;

class BarcodePrintService
{
    public static function handle(array $data)
    {
        $items = self::buildItems($data);

        return self::renderPdf($items);
    }

    protected static function buildItems(array $data): array
    {
        $productIds = $data['product_ids'] ?? [];
        $qty        = $data['qty'] ?? 1;

        $products = Product::with('baseUnit')
            ->whereIn('id', $productIds)
            ->get();

        $items = [];

        foreach ($products as $product) {

            $unit = $product->baseUnit;

            if (! $unit) {
                continue;
            }

            for ($i = 0; $i < $qty; $i++) {
                $items[] = [
                    'name'     => $product->name,
                    'sku'      => $product->sku,
                    'price'    => $unit->sell_price,
                    'barcode'  => BarcodeService::generate($product->sku),
                ];
            }
        }

        return $items;
    }

    protected static function renderPdf(array $items)
    {
        return Pdf::loadView('barcode.pdf', [
                'items' => $items
            ])
            ->setPaper('a4')
            ->stream('barcode.pdf');
    }
}