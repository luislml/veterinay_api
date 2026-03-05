<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Aseguramos que los roles existan
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $veterinaryRole = Role::firstOrCreate(['name' => 'veterinary']);
        $userRole = Role::firstOrCreate(['name' => 'user']);

        // Crear usuario ADMIN
        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin',
                'last_name' => 'Super',
                'phone' => '70000001',
                'password' => Hash::make('password123'),
            ]
        );
        $admin->assignRole($adminRole);

        // Crear usuario VETERINARY
        $vet = User::firstOrCreate(
            ['email' => 'veterinary@example.com'],
            [
                'name' => 'Veterinario',
                'last_name' => 'Especialista',
                'phone' => '70000002',
                'password' => Hash::make('password123'),
            ]
        );
        $vet->assignRole($veterinaryRole);

        // Crear usuario USER
        $normalUser = User::firstOrCreate(
            ['email' => 'user@example.com'],
            [
                'name' => 'Cliente',
                'last_name' => 'Final',
                'phone' => '70000003',
                'password' => Hash::make('password123'),
            ]
        );
        $normalUser->assignRole($userRole);
    }
}
