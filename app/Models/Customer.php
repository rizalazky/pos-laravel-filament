<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Customer extends Model
{
    protected $fillable = [
        'name',
        'phone_number',
        'points',
    ];

    public function transaction() : BelongsTo
    {
        return $this->belongsTo(Sale::class, 'customer_id');
    }
}
