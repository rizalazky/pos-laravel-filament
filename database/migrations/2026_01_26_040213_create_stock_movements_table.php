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
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained();

            // INPUT ASLI USER
            $table->foreignId('input_unit_id')->constrained('units');
            $table->decimal('input_quantity', 15, 4);
            $table->decimal('conversion_rate', 15, 6);
            // HASIL KONVERSI
            $table->decimal('base_quantity', 15, 4);

            $table->enum('type', ['in', 'out', 'adjust']);

            $table->decimal('stock_before', 15, 4);
            $table->decimal('stock_after', 15, 4);

            $table->string('reference_type')->nullable(); // purchase, sale, adjustment
            $table->unsignedBigInteger('reference_id')->nullable();

            $table->text('note')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};
