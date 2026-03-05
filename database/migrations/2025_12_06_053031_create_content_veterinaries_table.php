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
        Schema::create('content_veterinaries', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('veterinary_id');
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('type')->nullable(); // ejemplo: banner, promocion, info, etc.
            $table->timestamps();

            $table->foreign('veterinary_id')
                  ->references('id')
                  ->on('veterinaries')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('content_veterinaries');
    }
};
