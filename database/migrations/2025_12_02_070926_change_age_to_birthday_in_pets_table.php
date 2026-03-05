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
        Schema::table('pets', function (Blueprint $table) {
            // 1. Renombrar el campo age a birthday
            $table->renameColumn('age', 'birthday');

            // 2. Cambiar su tipo a DATE
            //    Necesita doctrine/dbal instalado si la columna ya existe.
            $table->date('birthday')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pets', function (Blueprint $table) {
            // revertir cambios
            $table->renameColumn('birthday', 'age');
            $table->integer('age')->change();
        });
    }
};
