<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Race;
use App\Models\TypePet;

class RaceSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            1 => [ // Perro
                "Mestizo (Criollo)", "Labrador Retriever", "Pastor Alemán", "Pitbull",
                "American Bully", "Poodle", "Shih Tzu", "Schnauzer Miniatura",
                "Chihuahua", "Yorkshire Terrier", "Golden Retriever", "Cocker Spaniel",
                "Bulldog Francés", "Beagle", "Husky Siberiano", "Rottweiler",
                "Dóberman", "Border Collie"
            ],
            2 => [ // Gato
                "Mestizo (Común Doméstico)", "Persa", "Siamés", "Maine Coon", "Bengalí",
                "Azul Ruso", "Sphynx", "British Shorthair", "Ragdoll", "Scottish Fold"
            ],
            3 => [ // Ave
                "Periquito Australiano", "Cacatúa Ninfa", "Canario",
                "Agapornis", "Loro Amazónico", "Loro Gris Africano", "Guacamayo"
            ],
            4 => [ // Conejo
                "Enano Holandés", "Mini Lop", "Mini Rex", "Lionhead", "Conejo Común"
            ],
            5 => [ // Hámster
                "Hámster Sirio", "Hámster Enano Ruso", "Hámster Roborowski", "Hámster Chino"
            ],
            6 => [ // Cuyo
                "Americano", "Abisinio", "Peruano"
            ],
            7 => [ // Tortuga
                "Tortuga de Orejas Rojas", "Tortuga de Caja", "Tortuga Morrocoy"
            ],
            8 => [ // Reptil
                "Gecko Leopardo", "Dragón Barbudo", "Pitón Bola", "Serpiente del Maíz"
            ],
            9 => [ // Pez
                "Betta", "Goldfish", "Guppy", "Molly", "Tetra Neón"
            ],
            12 => [ // Caballo
                "Caballo Criollo", "Cuarto de Milla", "Árabe"
            ],
            13 => [ // Cabra
                "Boer", "Saanen"
            ],
            14 => [ // Oveja
                "Merino", "Suffolk"
            ],
        ];

        foreach ($data as $typeId => $races) {
            foreach ($races as $race) {
                Race::create([
                    'name' => $race,
                    'type_pet_id' => $typeId,
                ]);
            }
        }
    }
}
