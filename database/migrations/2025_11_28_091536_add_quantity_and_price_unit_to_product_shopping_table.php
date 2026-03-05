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
        Schema::table('product_shopping', function (Blueprint $table) {
            $table->integer('quantity')->default(1); // Puedes cambiar el default si quieres
            $table->decimal('price_unit', 10, 2)->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_shopping', function (Blueprint $table) {
            $table->dropColumn(['quantity', 'price_unit']);
        });
    }
};
