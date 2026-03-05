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
        // Cambiar sales.date_sale a datetime
        Schema::table('sales', function (Blueprint $table) {
            $table->dateTime('date_sale')->change();
        });

        // Cambiar shoppings.date_shop a datetime
        Schema::table('shoppings', function (Blueprint $table) {
            $table->dateTime('date_shop')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revertir cambios a date
        Schema::table('sales', function (Blueprint $table) {
            $table->date('date_sale')->change();
        });

        Schema::table('shoppings', function (Blueprint $table) {
            $table->date('date_shop')->change();
        });
    }
};
