<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class PetsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $clients = DB::table('clients')->pluck('id');
        $raceIds = [1,2,3,4,5,6];
        $names = ['Firulais','Luna','Max','Bella','Rocky','Milo'];
        $colors = ['anaranjado','negro','blanco','marrón','gris','beige'];
        $genders = ['male','female'];

        foreach ($clients as $clientId) {
            for ($i = 0; $i < 6; $i++) {

                // Generar fecha de nacimiento entre 1 y 10 años atrás
                $birthday = Carbon::now()->subYears(rand(1, 10))
                                         ->subDays(rand(0, 365))
                                         ->format('Y-m-d');

                DB::table('pets')->insert([
                    'code'      => 'PET-' . strtoupper(Str::random(6)), 
                    'name'      => $names[$i % count($names)],
                    'race_id'   => $raceIds[$i % count($raceIds)],
                    'client_id' => $clientId,
                    'color'     => $colors[$i % count($colors)],
                    'gender'    => $genders[$i % count($genders)],
                    'birthday'  => $birthday, // <-- Campo corregido
                ]);
            }
        }
    }
}
