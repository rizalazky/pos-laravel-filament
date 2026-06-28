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
        Schema::table('sales', function (Blueprint $table) {
             $table->decimal('discount', 12, 2)->default(0)->before('created_by');
             $table->decimal('grand_total', 12, 2)->default(0);
             $table->decimal('total_payment', 12, 2)->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropColumn('discount');
            $table->dropColumn('grand_total');
            $table->dropColumn('total_payment');
        });
    }
};
