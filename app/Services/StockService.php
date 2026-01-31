<?php

namespace App\Services;

use App\Models\Product;
use App\Models\StockMovement;
use App\Models\ProductUnit;
use Illuminate\Support\Facades\DB;
use Exception;

class StockService
{
    //
    protected function getConversionRate(Product $product, int $unitId): float
    {
        if ($product->base_unit_id === $unitId) {
            return 1;
        }

        $productUnit = ProductUnit::where('product_id', $product->id)
            ->where('unit_id', $unitId)
            ->first();

        if (! $productUnit) {
            throw new Exception('Conversion rate not found for selected unit.');
        }

        return (float) $productUnit->conversion_rate;
    }

    protected function getStock(Product $product, int $unitId): float
    {
        if ($product->base_unit_id === $unitId) {
            return 1;
        }

        $productUnit = ProductUnit::where('product_id', $product->id)
            ->where('unit_id', $unitId)
            ->first();

        if (! $productUnit) {
            throw new Exception('Conversion rate not found for selected unit.');
        }

        return (float) $productUnit->stock;
    }

    public function stockIn(
        Product $product,
        int $unitId,
        float $qty,
        string $referenceType,
        int $referenceId,
        ?string $note = null
    ): void {
        DB::transaction(function () use (
            $product, $unitId, $qty, $referenceType, $referenceId, $note
        ) {
            $rate = $this->getConversionRate($product, $unitId);
            $baseQty = $qty * $rate;

            $before = $product->stock;
            $after  = $before + $baseQty;

            $product->update(['stock' => $after]);

            StockMovement::create([
                'product_id'       => $product->id,
                'input_unit_id'    => $unitId,
                'input_quantity'   => $qty,
                'conversion_rate'  => $rate,
                'base_quantity'    => $baseQty,
                'type'             => 'in',
                'stock_before'     => $before,
                'stock_after'      => $after,
                'reference_type'   => $referenceType,
                'reference_id'     => $referenceId,
                'note'             => $note,
                'created_by'       => auth()->id(),
            ]);
        });
    }

    public function stockOut(
        Product $product,
        int $unitId,
        float $qty,
        string $referenceType,
        int $referenceId,
        ?string $note = null
    ): void {
        DB::transaction(function () use (
            $product, $unitId, $qty, $referenceType, $referenceId, $note
        ) {
            $rate = $this->getConversionRate($product, $unitId);
            $baseQty = $qty * $rate;

            if ($product->stock < $baseQty) {
                throw new Exception('Stock not enough.');
            }

            $before = $product->stock;
            $after  = $before - $baseQty;

            $product->update(['stock' => $after]);

            StockMovement::create([
                'product_id'       => $product->id,
                'input_unit_id'    => $unitId,
                'input_quantity'   => $qty,
                'conversion_rate'  => $rate,
                'base_quantity'    => -$baseQty,
                'type'             => 'out',
                'stock_before'     => $before,
                'stock_after'      => $after,
                'reference_type'   => $referenceType,
                'reference_id'     => $referenceId,
                'note'             => $note,
                'created_by'       => auth()->id(),
            ]);
        });
    }

    public function adjust(
        Product $product,
        int $unitId,
        float $qty,
        ?string $note = null
    ): void {
        DB::transaction(function () use ($product, $unitId, $qty, $note) {
            $rate = $this->getConversionRate($product, $unitId);
            $baseQty = $qty * $rate;

            $before = $product->stock;
            $after  = $before + $baseQty;

            if ($after < 0) {
                throw new Exception('Adjustment causes negative stock.');
            }

            $product->update(['stock' => $after]);

            StockMovement::create([
                'product_id'       => $product->id,
                'input_unit_id'    => $unitId,
                'input_quantity'   => $qty,
                'conversion_rate'  => $rate,
                'base_quantity'    => $baseQty,
                'type'             => 'adjust',
                'stock_before'     => $before,
                'stock_after'      => $after,
                'reference_type'   => 'adjustment',
                'note'             => $note,
                'created_by'       => auth()->id(),
            ]);
        });
    }

    public function rollbackByReference(string $referenceType, int $referenceId): void
    {
        DB::transaction(function () use ($referenceType, $referenceId) {

            $movements = StockMovement::where('reference_type', $referenceType)
                ->where('reference_id', $referenceId)
                ->orderByDesc('id') // penting!
                ->lockForUpdate()
                ->get();

            if ($movements->isEmpty()) {
                return;
            }

            foreach ($movements as $movement) {
                $product = Product::lockForUpdate()->find($movement->product_id);

                if (! $product) {
                    continue;
                }

                // Balikin stok ke sebelum movement
                $product->update([
                    'stock' => $movement->stock_before,
                ]);
            }

            // Hapus movement lama (atau soft delete kalau mau)
            StockMovement::where('reference_type', $referenceType)
                ->where('reference_id', $referenceId)
                ->delete();
        });
    }


}
