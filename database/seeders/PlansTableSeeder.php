<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PlansTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('plans')->insert([
            [
                'name' => 'free',
                'description' => 'Incluye acceso limitado a funciones estándar.',
                'type' => 'basic',
            ],
            [
                'name' => 'Profesional',
                'description' => 'Acceso completo, ideal para veterinarias medianas.',
                'type' => 'quarterly',
            ],
            [
                'name' => 'Premium',
                'description' => 'Todas las funciones desbloqueadas con soporte prioritario.',
                'type' => 'annual',
            ],
        ]);
    }
}
