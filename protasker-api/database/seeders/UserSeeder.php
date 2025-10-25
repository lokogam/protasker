<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear usuario administrador
        $admin = User::create([
            'name' => 'Administrador',
            'email' => 'admin@protasker.com',
            'password' => Hash::make('password'),
        ]);
        $admin->assignRole('administrador');

        // Crear usuario desarrollador
        $developer = User::create([
            'name' => 'Desarrollador',
            'email' => 'dev@protasker.com',
            'password' => Hash::make('password'),
        ]);
        $developer->assignRole('desarrollador');
    }
}