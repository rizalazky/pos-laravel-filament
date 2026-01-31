<?php

namespace App\Services;

use App\Models\Purchase;
use App\Models\Product;
use App\Models\ProductUnit;
use Illuminate\Support\Facades\DB;
use Exception;

class PurchaseService
{
    public function create(array $data): Purchase
    {
        return DB::transaction(function () use ($data) {

            $purchase = Purchase::create([
                'date'           => $data['date'],
                'invoice_number' => $data['invoice_number'],
                'supplier_id'    => $data['supplier_id'] ?? null,
                'note'           => $data['note'] ?? null,
                'total'          => 0,
            ]);

            $total = 0;


            foreach ($data['items'] as $item) {

                $product = Product::findOrFail($item['product_id']);

                $rate = $this->getConversionRate($product, $item['unit_id']);
                $baseQty = $item['quantity'] * $rate;

                $subtotal = $item['quantity'] * $item['price'];
                $total += $subtotal;

                $purchase->items()->create([
                    'product_id'     => $product->id,
                    'unit_id'        => $item['unit_id'],
                    'quantity'       => $item['quantity'],
                    'conversion_rate'=> $rate,
                    'base_quantity'  => $baseQty,
                    'price'          => $item['price'],
                    'subtotal'       => $subtotal,
                ]);

                // 🔥 STOK MASUK
                app(StockService::class)->stockIn(
                    $product,
                    $item['unit_id'],
                    $item['quantity'],
                    'purchase',
                    $purchase->id
                );
            }

            $purchase->update(['total' => $total]);

            return $purchase;
        });
    }

    public function update(Purchase $purchase, array $data): Purchase
    {
        return DB::transaction(function () use ($purchase, $data) {

            // 1️⃣ rollback stok lama
            app(StockService::class)
                ->rollbackByReference('purchase', $purchase->id);

            // 2️⃣ hapus item lama
            $purchase->items()->delete();

            // 3️⃣ update header
            $purchase->update([
                'date'        => $data['date'],
                'supplier_id' => $data['supplier_id'] ?? null,
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

                $purchase->items()->create([
                    'product_id'      => $product->id,
                    'unit_id'         => $item['unit_id'],
                    'quantity'        => $item['quantity'],
                    'conversion_rate' => $rate,
                    'base_quantity'   => $baseQty,
                    'price'           => $item['price'],
                    'subtotal'        => $subtotal,
                ]);

                app(StockService::class)->stockIn(
                    $product,
                    $item['unit_id'],
                    $item['quantity'],
                    'purchase',
                    $purchase->id
                );
            }

            $purchase->update(['total' => $total]);

            return $purchase;
        });
    }

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

    


    public function delete(Purchase $purchase): void
    {
        DB::transaction(function () use ($purchase) {

            // 1. rollback stock dari movement
            app(StockService::class)
                ->rollbackByReference('purchase', $purchase->id);

            // 3. hapus items
            $purchase->items()->delete();

            // 4. hapus purchase
            $purchase->delete();
        });
    }

}
