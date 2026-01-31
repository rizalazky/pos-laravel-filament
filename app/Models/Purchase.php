<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    //
    // use HasFactory;

    protected $fillable = [
        'supplier_id',
        'date',
        'invoice_number',
        'status',
        'total',
        'note',
    ];

    protected $casts = [
        'date' => 'date',
        'total' => 'decimal:2',
    ];

    // ================= Relations =================

    public function items()
    {
        return $this->hasMany(PurchaseItem::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }
}
