<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockOpname extends Model
{
    //
    protected $fillable = ['code', 'date', 'status', 'note'];

    public function items()
    {
        return $this->hasMany(StockOpnameItem::class);
    }
}
