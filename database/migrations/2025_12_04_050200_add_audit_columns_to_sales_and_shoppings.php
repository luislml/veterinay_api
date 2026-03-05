<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Sales table
        Schema::table('sales', function (Blueprint $table) {
            // Agregar SoftDeletes si no existen
            if (!Schema::hasColumn('sales', 'deleted_at')) {
                $table->softDeletes(); // agrega deleted_at
            }

            $table->unsignedBigInteger('created_by')->nullable()->after('id');
            $table->unsignedBigInteger('updated_by')->nullable()->after('created_by');
            $table->unsignedBigInteger('deleted_by')->nullable()->after('deleted_at');
        });

        // Shoppings table
        Schema::table('shoppings', function (Blueprint $table) {
            if (!Schema::hasColumn('shoppings', 'deleted_at')) {
                $table->softDeletes();
            }

            $table->unsignedBigInteger('created_by')->nullable()->after('id');
            $table->unsignedBigInteger('updated_by')->nullable()->after('created_by');
            $table->unsignedBigInteger('deleted_by')->nullable()->after('deleted_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropColumn(['created_by', 'updated_by', 'deleted_by']);

            if (Schema::hasColumn('sales', 'deleted_at')) {
                $table->dropSoftDeletes();
            }
        });

        Schema::table('shoppings', function (Blueprint $table) {
            $table->dropColumn(['created_by', 'updated_by', 'deleted_by']);

            if (Schema::hasColumn('shoppings', 'deleted_at')) {
                $table->dropSoftDeletes();
            }
        });
    }
};

