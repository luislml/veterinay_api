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
        // Clients
        Schema::table('clients', function (Blueprint $table) {
            $table->unsignedBigInteger('created_by')->nullable()->after('id');
            $table->unsignedBigInteger('updated_by')->nullable()->after('created_by');
            $table->unsignedBigInteger('deleted_by')->nullable()->after('deleted_at');
        });

        // Pets
        Schema::table('pets', function (Blueprint $table) {
            $table->unsignedBigInteger('created_by')->nullable()->after('id');
            $table->unsignedBigInteger('updated_by')->nullable()->after('created_by');
            $table->unsignedBigInteger('deleted_by')->nullable()->after('deleted_at');

            // Cambiar age de integer a string
            $table->string('age')->change();
        });

        // Users
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('created_by')->nullable()->after('id');
            $table->unsignedBigInteger('updated_by')->nullable()->after('created_by');
            $table->unsignedBigInteger('deleted_by')->nullable()->after('deleted_at');
        });

        // Veterinaries
        Schema::table('veterinaries', function (Blueprint $table) {
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
        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn(['created_by','updated_by','deleted_by']);
        });

        Schema::table('pets', function (Blueprint $table) {
            $table->dropColumn(['created_by','updated_by','deleted_by']);
            $table->integer('age')->change();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['created_by','updated_by','deleted_by']);
        });

        Schema::table('veterinaries', function (Blueprint $table) {
            $table->dropColumn(['created_by','updated_by','deleted_by']);
        });
    }
};
