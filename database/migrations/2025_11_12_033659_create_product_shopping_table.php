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
        Schema::create('product_shopping', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id');  // FK a products
            $table->unsignedBigInteger('shopping_id'); // FK a shopping
            $table->timestamps();

            // Llaves foráneas
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('shopping_id')->references('id')->on('shoppings')->onDelete('cascade');

            // Evita duplicados
            $table->unique(['product_id', 'shopping_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_shopping');
    }
};
