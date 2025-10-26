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
        $this->command->info('🚀 Iniciando proceso de seeders...');

        // Sembrar roles primero
        $this->command->info('📝 Creando roles...');
        $this->call(RoleSeeder::class);

        // Luego sembrar usuarios con roles asignados
        $this->command->info('👥 Creando usuarios...');
        $this->call(UserSeeder::class);

        // Crear proyectos
        $this->command->info('📁 Creando proyectos...');
        $this->call(ProjectSeeder::class);

        // Crear tareas para los proyectos
        $this->command->info('📋 Creando tareas...');
        $this->call(TaskSeeder::class);

        $this->command->info('');
        $this->command->info('🎉 ¡Seeders completados exitosamente!');
        $this->command->info('💡 Ahora puedes iniciar sesión con:');
        $this->command->info('   Admin: admin@example.com / password');
        $this->command->info('   Dev: dev@example.com / password');
    }
}
