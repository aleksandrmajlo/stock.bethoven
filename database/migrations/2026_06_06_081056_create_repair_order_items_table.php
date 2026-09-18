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
        Schema::create('repair_order_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('repair_order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('kit_part_id')->constrained()->cascadeOnDelete();

            $table->string('barcode');
            $table->string('title');

            $table->decimal('purchase_price', 10, 2)->default(0);
            $table->decimal('sale_price', 10, 2)->default(0);

            $table->timestamps();

            $table->unique('kit_part_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('repair_order_items');
    }
};
