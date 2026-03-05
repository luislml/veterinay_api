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
        Schema::table('movements', function (Blueprint $table) {
            // Relación opcional con ventas
            $table->unsignedBigInteger('sale_id')->nullable()->after('veterinary_id');
            $table->foreign('sale_id')
                ->references('id')->on('sales')
                ->onDelete('set null');

            // Relación opcional con compras
            $table->unsignedBigInteger('shopping_id')->nullable()->after('sale_id');
            $table->foreign('shopping_id')
                ->references('id')->on('shoppings')
                ->onDelete('set null');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('movements', function (Blueprint $table) {

            if (Schema::hasColumn('movements', 'sale_id')) {
                $table->dropForeign(['sale_id']);
                $table->dropColumn('sale_id');
            }

            if (Schema::hasColumn('movements', 'shopping_id')) {
                $table->dropForeign(['shopping_id']);
                $table->dropColumn('shopping_id');
            }
        });
    }
};
