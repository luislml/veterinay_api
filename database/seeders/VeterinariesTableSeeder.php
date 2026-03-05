<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class VeterinariesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Para usuario 1
        $vetsUser1 = [
            ['name' => 'Veterinaria Central', 'plan_id' => 1],
            ['name' => 'Clinica Vida Animal', 'plan_id' => 1],
            ['name' => 'Pet Mundo', 'plan_id' => 1],
        ];

        // Para usuario 2
        $vetsUser2 = [
            ['name' => 'Veterinaria San Roque', 'plan_id' => 1],
            ['name' => 'Animal Care Plus', 'plan_id' => 1],
            ['name' => 'Veterinaria Potosí', 'plan_id' => 1],
        ];

        // Insertar veterinarias del usuario 1
        foreach ($vetsUser1 as $vet) {
            $id = DB::table('veterinaries')->insertGetId([
                'name'    => $vet['name'],
                'slug'    => Str::slug($vet['name']),
                'plan_id' => $vet['plan_id'],
            ]);

            DB::table('user_veterinary')->insert([
                'user_id'       => 1,
                'veterinary_id' => $id,
            ]);
        }

        // Insertar veterinarias del usuario 2
        foreach ($vetsUser2 as $vet) {
            $id = DB::table('veterinaries')->insertGetId([
                'name'    => $vet['name'],
                'slug'    => Str::slug($vet['name']),
                'plan_id' => $vet['plan_id'],
            ]);

            DB::table('user_veterinary')->insert([
                'user_id'       => 2,
                'veterinary_id' => $id,
            ]);
        }
    }
}
