<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Paso 1: Expandir el ENUM para incluir tanto valores antiguos como nuevos
        Schema::table('shoppings', function (Blueprint $table) {
            $table->enum('state', ['pending', 'completed', 'canceled', 'Completado', 'Cancelado'])->change();
        });

        // Paso 2: Actualizar los valores existentes de inglés a español
        DB::table('shoppings')->where('state', 'pending')->update(['state' => 'Completado']);
        DB::table('shoppings')->where('state', 'completed')->update(['state' => 'Completado']);
        DB::table('shoppings')->where('state', 'canceled')->update(['state' => 'Cancelado']);

        // Paso 3: Restringir el ENUM solo a los valores en español
        Schema::table('shoppings', function (Blueprint $table) {
            $table->enum('state', ['Completado', 'Cancelado'])->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revertir a los valores en inglés
        DB::table('shoppings')->where('state', 'Completado')->update(['state' => 'completed']);
        DB::table('shoppings')->where('state', 'Cancelado')->update(['state' => 'canceled']);

        Schema::table('shoppings', function (Blueprint $table) {
            $table->enum('state', ['pending', 'completed', 'canceled'])->change();
        });
    }
};
