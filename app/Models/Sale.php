<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    //
    protected $fillable = [
        'date',
        'invoice_number',
        'customer_id',
        'note',
        'total',
        'created_by',
    ];

    public function items()
    {
        return $this->hasMany(SaleItem::class);
    }
}
