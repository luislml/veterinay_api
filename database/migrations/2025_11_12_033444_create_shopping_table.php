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
        Schema::create('shoppings', function (Blueprint $table) {
            $table->id();
            $table->date('date_shop');                 // fecha de la compra
            $table->enum('state', ['pending', 'completed', 'canceled']); // estado
            $table->decimal('amount', 10, 2);         // monto total
            $table->unsignedBigInteger('user_id');    // FK a users
            $table->timestamps();

            // Llave foránea
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shoppings');
    }
};
