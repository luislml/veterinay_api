<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\TypePet;

class TypePetSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            ['id' => 1, 'name' => 'Perro'],
            ['id' => 2, 'name' => 'Gato'],
            ['id' => 3, 'name' => 'Ave'],
            ['id' => 4, 'name' => 'Conejo'],
            ['id' => 5, 'name' => 'Hámster'],
            ['id' => 6, 'name' => 'Cuyo'],
            ['id' => 7, 'name' => 'Tortuga'],
            ['id' => 8, 'name' => 'Reptil'],
            ['id' => 9, 'name' => 'Pez'],
            ['id' => 12, 'name' => 'Caballo'],
            ['id' => 13, 'name' => 'Cabra'],
            ['id' => 14, 'name' => 'Oveja'],
        ];

        foreach ($types as $type) {
            TypePet::updateOrCreate(['id' => $type['id']], ['name' => $type['name']]);
        }
    }
}
