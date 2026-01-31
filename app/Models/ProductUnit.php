<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductUnit extends Model
{
    //
    protected $fillable = [
        'product_id',
        'unit_id',
        'conversion_rate',
        'is_base',
        'cost_price',
        'sell_price',
        'stock',
        'barcode',
        'is_default',
        'is_active',
    ];

    protected $casts = [
        'is_base'    => 'boolean',
        'is_default' => 'boolean',
        'is_active'  => 'boolean',
    ];

    /* ================= RELATION ================= */

    public function product() : BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function unit() : BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    /* ================= HELPER ================= */

    /**
     * Convert input qty ke base qty
     */
    public function toBaseQty(float $qty): float
    {
        return $qty * $this->conversion_rate;
    }

    /**
     * Scope: hanya unit base
     */
    public function scopeBase($query)
    {
        return $query->where('is_base', true);
    }
}
