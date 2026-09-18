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
        Schema::table('kit_parts', function (Blueprint $table) {
            $table->unsignedInteger('sequence_number')->nullable()->after('kit_id');
            $table->timestamp('printed_at')->nullable()->after('status');
            $table->unique(['kit_id', 'sequence_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kit_parts', function (Blueprint $table) {
            //
        });
    }
};
