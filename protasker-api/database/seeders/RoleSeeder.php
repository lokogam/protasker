<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear rol de administrador
        Role::create(['name' => 'administrador']);

        // Crear rol de desarrollador
        Role::create(['name' => 'desarrollador']);
    }
}
