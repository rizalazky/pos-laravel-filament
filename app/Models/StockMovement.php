<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StockMovement extends Model
{
    use HasFactory;

    protected $table = 'stock_movements';

    protected $fillable = [
        'product_id',

        // input user
        'input_unit_id',
        'input_quantity',
        'conversion_rate',

        // hasil konversi
        'base_quantity',

        'type',

        'stock_before',
        'stock_after',

        'reference_type',
        'reference_id',

        'note',
        'created_by',
    ];

    protected $casts = [
        'input_quantity'   => 'decimal:4',
        'conversion_rate'  => 'decimal:6',
        'base_quantity'    => 'decimal:4',
        'stock_before'     => 'decimal:4',
        'stock_after'      => 'decimal:4',
    ];

    // ================= Relations =================

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class, 'input_unit_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Polymorphic-like reference (manual)
     * purchase / sale / adjustment
     */
    public function reference()
    {
        return $this->morphTo(
            __FUNCTION__,
            'reference_type',
            'reference_id'
        );
    }
}
