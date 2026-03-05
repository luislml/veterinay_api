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
        Schema::create('configuration', function (Blueprint $table) {
            $table->id();
            $table->string('style');                   // estilo de la configuración
            $table->unsignedBigInteger('veterinary_id'); // FK a veterinaries
            $table->timestamps();

            // Llave foránea
            $table->foreign('veterinary_id')->references('id')->on('veterinaries')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('configuration');
    }
};
