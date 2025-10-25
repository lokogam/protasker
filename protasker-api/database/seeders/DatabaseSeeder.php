<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Sembrar roles primero
        $this->call(RoleSeeder::class);

        // Luego sembrar usuarios con roles asignados
        $this->call(UserSeeder::class);
    }
}
