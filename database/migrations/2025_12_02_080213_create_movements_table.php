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
        Schema::create('movements', function (Blueprint $table) {
            $table->id();
            // FK a veterinarias
            $table->unsignedBigInteger('veterinary_id');
            $table->foreign('veterinary_id')
                  ->references('id')
                  ->on('veterinaries')
                  ->onDelete('cascade');

            // Tipo de movimiento
            $table->enum('type', ['entrada', 'salida']);

            // Monto
            $table->decimal('amount', 15, 2);

            // Detalle opcional
            $table->text('detail')->nullable();

            // Usuarios que crearon, actualizaron, eliminaron
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('movements');
    }
};
