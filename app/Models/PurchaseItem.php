<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PurchaseItem extends Model
{
    //
    // use HasFactory;

    protected $fillable = [
        'purchase_id',
        'product_id',

        'unit_id',        // unit yang dipilih user
        'quantity',            // qty input user
        'conversion_rate',    // conversion rate ke base unit
        'base_quantity',       // hasil konversi ke base unit

        'price',
        'subtotal',
    ];

    protected $casts = [
        'qty' => 'decimal:4',
        'qty_base' => 'decimal:4',
        // 'price' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    // ================= Relations =================

    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }
}
