<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Relations\Relation;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Relation::morphMap([
            'purchase' => \App\Models\Purchase::class,
            'sale' => \App\Models\Sale::class,
            'stock_opname' => \App\Models\StockOpname::class,
            'stock_adjustment' => \App\Models\StockAdjustment::class,
            'stock_transfer' => \App\Models\StockTransfer::class,
        ]);
    }
}
