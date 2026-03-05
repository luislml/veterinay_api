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
        Schema::create('pets', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->unsignedBigInteger('race_id');
        $table->unsignedBigInteger('client_id');
        $table->string('color')->nullable();
        $table->enum('gender', ['male', 'female'])->nullable();
        $table->integer('age')->nullable();
        $table->timestamps();

        // Relaciones
        $table->foreign('race_id')
              ->references('id')
              ->on('races')
              ->onDelete('cascade');

        $table->foreign('client_id')
              ->references('id')
              ->on('clients')
              ->onDelete('cascade');
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pets');
    }
};
