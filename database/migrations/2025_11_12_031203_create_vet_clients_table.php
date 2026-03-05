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
        Schema::create('vet_clients', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('veterinary_id');
            $table->unsignedBigInteger('client_id');
            $table->timestamps();

            $table->foreign('veterinary_id')->references('id')->on('veterinaries')->onDelete('cascade');
            $table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade');

            $table->unique(['veterinary_id', 'client_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vet_clients');
    }
};
