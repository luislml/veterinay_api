<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('pets', function (Blueprint $table) {
        $table->string('code')->nullable()->after('id');
        });

        DB::table('pets')->get()->each(function ($pet) {
            DB::table('pets')
                ->where('id', $pet->id)
                ->update(['code' => 'PET' . strtoupper(Str::random(6))]);
        });

        Schema::table('pets', function (Blueprint $table) {
            $table->string('code')->unique()->nullable(false)->change();
        });
    }

   
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pets', function (Blueprint $table) {
            $table->dropColumn('code');
        });
    }
};
