<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        //
        Schema::table('product_units', function (Blueprint $table) {

            // $table->dropForeign(['product_variant_id']);
            $table->renameColumn('product_variant_id', 'product_id');

            $table->foreign('product_id')
                ->references('id')
                ->on('products')
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::table('product_units', function (Blueprint $table) {

            $table->dropForeign(['product_id']);
            $table->renameColumn('product_id', 'product_variant_id');

            $table->foreign('product_variant_id')
                ->references('id')
                ->on('product_variants')
                ->cascadeOnDelete();
        });
    }
};
