<?php

namespace App\Services;

use App\Models\Sale;
use App\Models\Product;
use App\Models\ProductUnit;
use Illuminate\Support\Facades\DB;
use Exception;

class SaleService
{

    protected function getConversionRate(Product $product, int $unitId): float
    {
        if ($product->base_unit_id === $unitId) {
            return 1;
        }

        $unit = ProductUnit::where('product_id', $product->id)
            ->where('unit_id', $unitId)
            ->first();

        if (! $unit) {
            throw new Exception('Conversion rate not found.');
        }

        return (float) $unit->conversion_rate;
    }

    public function create(array $data): Sale
    {
        return DB::transaction(function () use ($data) {

            $sale = Sale::create([
                'date'           => $data['date'],
                'invoice_number' => $data['invoice_number'],
                'customer_id'    => $data['customer_id'] ?? null,
                'note'           => $data['note'] ?? null,
                'total'          => 0,
                'created_by'     => auth()->id(),
            ]);

            $total = 0;

            foreach ($data['items'] as $item) {

                $product = Product::lockForUpdate()->findOrFail($item['product_id']);

                $rate = $this->getConversionRate($product, $item['unit_id']);
                $baseQty = $item['quantity'] * $rate;

                $subtotal = $item['quantity'] * $item['price'];
                $total += $subtotal;

                $sale->items()->create([
                    'product_id'      => $product->id,
                    'unit_id'         => $item['unit_id'],
                    'quantity'        => $item['quantity'],
                    'conversion_rate' => $rate,
                    'base_quantity'   => $baseQty,
                    'price'           => $item['price'],
                    'subtotal'        => $subtotal,
                ]);

                // 🔻 STOCK OUT
                app(StockService::class)->stockOut(
                    $product,
                    $item['unit_id'],
                    $item['quantity'],
                    'sale',
                    $sale->id
                );
            }

            $sale->update(['total' => $total]);

            return $sale;
        });
    }

    public function update(Sale $sale, array $data): Sale
    {
        return DB::transaction(function () use ($sale, $data) {

            // 1️⃣ rollback stok lama
            app(StockService::class)
                ->rollbackByReference('sale', $sale->id);

            // 2️⃣ hapus item lama
            $sale->items()->delete();

            // 3️⃣ update header
            $sale->update([
                'date'        => $data['date'],
                'customer_id' => $data['customer_id'] ?? null,
                'note'        => $data['note'] ?? null,
            ]);

            $total = 0;

            // 4️⃣ buat ulang item + stok
            foreach ($data['items'] as $item) {

                $product = Product::findOrFail($item['product_id']);
                $rate = $this->getConversionRate($product, $item['unit_id']);
                $baseQty = $item['quantity'] * $rate;

                $subtotal = $item['quantity'] * $item['price'];
                $total += $subtotal;

                $sale->items()->create([
                    'product_id'      => $product->id,
                    'unit_id'         => $item['unit_id'],
                    'quantity'        => $item['quantity'],
                    'conversion_rate' => $rate,
                    'base_quantity'   => $baseQty,
                    'price'           => $item['price'],
                    'subtotal'        => $subtotal,
                ]);

                app(StockService::class)->stockOut(
                    $product,
                    $item['unit_id'],
                    $item['quantity'],
                    'sale',
                    $sale->id
                );
            }

            $sale->update(['total' => $total]);

            return $sale;
        });
    }

    

    


    public function delete(Sale $sale): void
    {
        DB::transaction(function () use ($sale) {

            // 1. rollback stock dari movement
            app(StockService::class)
                ->rollbackByReference('sale', $sale->id);

            // 3. hapus items
            $sale->items()->delete();

            // 4. hapus sale
            $sale->delete();
        });
    }
}
