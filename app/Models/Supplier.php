<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Models\PurchaseItem;


class Supplier extends Model
{
    //
    protected $fillable = [
        'name',
        'phone_number',
        'address',
    ];

    public function purchases() : HasMany
    {
        return $this->hasMany(Purchase::class, 'supplier_id');
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class)
            ->withPivot([
                'supplier_sku',
                'is_default',
            ])
            ->withTimestamps();
    }

    public function latestPurchaseOfProduct(Product $product): ?PurchaseItem
    {
        return PurchaseItem::query()
            ->where('product_id', $product->id)
            ->whereHas('purchase', function ($query) {
                $query->where('supplier_id', $this->id);
            })
            ->latest()
            ->first();
    }
}
