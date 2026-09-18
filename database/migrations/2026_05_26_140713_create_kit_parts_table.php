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
        Schema::create('kit_parts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kit_id')
                ->constrained('kits')
                ->cascadeOnDelete();
            $table->string('title');
            // Например: Колба / Резинка / Отбойник / Подушка
            $table->string('barcode')->unique()->nullable();
            $table->decimal('purchase_price', 10, 2)->default(0);
            $table->decimal('sale_price', 10, 2)->default(0);

            $table->string('status')->default('in_stock');
            // in_stock / sold

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kit_parts');
    }
};
