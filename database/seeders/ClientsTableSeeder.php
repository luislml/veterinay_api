<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB; 

class ClientsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clientes para veterinarias del usuario 1
        $clientsUser1 = [
            [
                'name' => 'Carlos',
                'last_name' => 'López',
                'phone' => '70000001',
                'address' => 'Av. Bolívar 123',
                'ci' => '12345601',
                'veterinaries' => [1],
            ],
            [
                'name' => 'María',
                'last_name' => 'Rivera',
                'phone' => '70000002',
                'address' => 'Calle Sucre 55',
                'ci' => '12345602',
                'veterinaries' => [2],
            ],
            [
                'name' => 'Jorge',
                'last_name' => 'Dávila',
                'phone' => '70000003',
                'address' => 'Zona Miraflores 890',
                'ci' => '12345603',
                'veterinaries' => [3],
            ],
        ];

        // Clientes para veterinarias del usuario 2
        $clientsUser2 = [
            [
                'name' => 'Ana',
                'last_name' => 'Quispe',
                'phone' => '70000004',
                'address' => 'Av. Sevilla',
                'ci' => '12345604',
                'veterinaries' => [4],
            ],
            [
                'name' => 'Luis',
                'last_name' => 'Catari',
                'phone' => '70000005',
                'address' => 'Villa Imperial',
                'ci' => '12345605',
                'veterinaries' => [5],
            ],
            [
                'name' => 'Roxana',
                'last_name' => 'Pérez',
                'phone' => '70000006',
                'address' => 'Zona San Benito',
                'ci' => '12345606',
                'veterinaries' => [6],
            ],
        ];

        // Insertar y asociar clientes del usuario 1
        foreach ($clientsUser1 as $client) {
            $clientId = DB::table('clients')->insertGetId([
                'name'       => $client['name'],
                'last_name'  => $client['last_name'],
                'phone'      => $client['phone'],
                'address'    => $client['address'],
                'ci'         => $client['ci'],
            ]);

            foreach ($client['veterinaries'] as $vetId) {
                DB::table('vet_clients')->insert([
                    'client_id'     => $clientId,
                    'veterinary_id' => $vetId,
                ]);
            }
        }

        // Insertar y asociar clientes del usuario 2
        foreach ($clientsUser2 as $client) {
            $clientId = DB::table('clients')->insertGetId([
                'name'       => $client['name'],
                'last_name'  => $client['last_name'],
                'phone'      => $client['phone'],
                'address'    => $client['address'],
                'ci'         => $client['ci'],
            ]);

            foreach ($client['veterinaries'] as $vetId) {
                DB::table('vet_clients')->insert([
                    'client_id'     => $clientId,
                    'veterinary_id' => $vetId,
                ]);
            }
        }
    }
}
