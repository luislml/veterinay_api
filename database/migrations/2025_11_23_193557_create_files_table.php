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
        Schema::create('files', function (Blueprint $table) {
            $table->id();
            $table->string('type');      // tipo o categoría del archivo (ej: image, doc, etc)
            $table->string('name');      // img_1, img_2, ...
            $table->string('extension'); // jpg, png, pdf, etc
            $table->morphs('fileable');  // fileable_id + fileable_type
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('files');
    }
};
