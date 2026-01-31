<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;


class Product extends Model
{
    //
    protected $fillable = [
        'name',
        'category_id',
        'parent_id',
        'sku',
        'is_active',
        'stock'
    ];

    /* ================= RELATION ================= */

    public function category() : BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    // Variant → Parent
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'parent_id');
    }

    // Parent → Variants
    public function variants(): HasMany
    {
        return $this->hasMany(Product::class, 'parent_id');
    }


    /*
    |--------------------------------------------------------------------------
    | Units (harga & konversi)
    |--------------------------------------------------------------------------
    */

    public function units(): HasMany
    {
        return $this->hasMany(ProductUnit::class);
    }

    public function baseUnit()
    {
        return $this->hasOne(ProductUnit::class)
            ->where('is_base', true);
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    // hanya product utama
    public function scopeParentOnly($query)
    {
        return $query->whereNull('parent_id');
    }

    // hanya variant
    public function scopeVariantOnly($query)
    {
        return $query->whereNotNull('parent_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    public function isParent(): bool
    {
        return is_null($this->parent_id);
    }

    public function isVariant(): bool
    {
        return ! is_null($this->parent_id);
    }
}
