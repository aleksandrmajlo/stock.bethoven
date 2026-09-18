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
        Schema::create('repair_orders', function (Blueprint $table) {

            $table->id();

            $table->string('client_name')->nullable();

            $table->string('client_phone')->nullable();

            $table->string('car_info')->nullable();

            $table->string('master_name')->nullable();

            $table->decimal('total_amount', 10, 2)->default(0);

            $table->string('status')->default('draft');

            // draft / payment_due / paid / cancelled

            $table->timestamp('paid_at')->nullable();

            $table->foreignId('confirmed_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('repair_orders');
    }
};
