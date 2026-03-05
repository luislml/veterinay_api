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
        Schema::table('reservations', function (Blueprint $table) {
            // Quitar la columna client_id y la relación
            $table->dropForeign(['client_id']);
            $table->dropColumn('client_id');

            // Agregar nuevas columnas
            $table->string('name_reservation')->after('veterinary_id');
            $table->string('last_name_reservation')->after('name_reservation');
            $table->string('ci_reservation')->after('last_name_reservation');
            $table->string('phone_reservation')->after('ci_reservation');
            $table->text('details')->nullable()->after('phone_reservation');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            // Restaurar client_id
            $table->unsignedBigInteger('client_id')->after('id');

            $table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade');

            // Quitar las nuevas columnas
            $table->dropColumn([
                'name_reservation',
                'last_name_reservation',
                'ci_reservation',
                'phone_reservation',
                'details'
            ]);
        });
    }
};
