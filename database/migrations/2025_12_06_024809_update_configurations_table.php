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
        Schema::table('configuration', function (Blueprint $table) {
            // Agregar campos nuevos
            $table->string('color_primary')->nullable()->after('id');
            $table->string('color_secondary')->nullable()->after('color_primary');

            $table->text('about_us')->nullable()->after('color_secondary');
            $table->text('description_team')->nullable()->after('about_us');

            $table->string('phone')->nullable()->after('description_team');
            $table->string('phone_emergency')->nullable()->after('phone');

            // Eliminar campo style
            $table->dropColumn('style');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('configuration', function (Blueprint $table) {
            // Revertir agregados
            $table->dropColumn([
                'color_primary',
                'color_secondary',
                'about_us',
                'description_team',
                'phone',
                'phone_emergency',
            ]);

            // Restaurar campo eliminado
            $table->string('style')->nullable();
        });
    }
};
