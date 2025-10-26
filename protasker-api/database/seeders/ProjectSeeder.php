<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener usuarios para asignar proyectos
        $users = User::all();

        if ($users->isEmpty()) {
            $this->command->info('No hay usuarios disponibles. Ejecuta UserSeeder primero.');
            return;
        }

        $projects = [
            [
                'name' => 'Sistema de Gestión de Inventario',
                'description' => 'Desarrollo de un sistema completo para gestionar el inventario de productos, con funcionalidades de entrada, salida, reportes y alertas de stock mínimo.',
                'user_id' => $users->random()->id,
                'progress' => 0.00,
                'start_date' => now()->addDays(5),
                'end_date' => now()->addDays(60),
                'status' => 'pending',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Plataforma de E-learning',
                'description' => 'Creación de una plataforma educativa online con cursos, evaluaciones, certificaciones y seguimiento de progreso de estudiantes.',
                'user_id' => $users->random()->id,
                'progress' => 35.50,
                'start_date' => now()->subDays(15),
                'end_date' => now()->addDays(45),
                'status' => 'in_progress',
                'created_at' => now()->subDays(15),
                'updated_at' => now(),
            ],
            [
                'name' => 'API de Pagos Integrada',
                'description' => 'Desarrollo de una API RESTful para procesar pagos con múltiples proveedores, incluyendo validaciones de seguridad y webhooks.',
                'user_id' => $users->random()->id,
                'progress' => 75.00,
                'start_date' => now()->subDays(30),
                'end_date' => now()->addDays(10),
                'status' => 'in_progress',
                'created_at' => now()->subDays(30),
                'updated_at' => now(),
            ],
            [
                'name' => 'App Móvil de Delivery',
                'description' => 'Aplicación móvil para delivery de comida con geolocalización, pagos online, seguimiento en tiempo real y sistema de calificaciones.',
                'user_id' => $users->random()->id,
                'progress' => 100.00,
                'start_date' => now()->subDays(90),
                'end_date' => now()->subDays(10),
                'status' => 'completed',
                'created_at' => now()->subDays(90),
                'updated_at' => now()->subDays(10),
            ],
            [
                'name' => 'Dashboard de Analytics',
                'description' => 'Panel de control con métricas y KPIs en tiempo real, gráficos interactivos y reportes exportables para análisis de negocio.',
                'user_id' => $users->random()->id,
                'progress' => 90.25,
                'start_date' => now()->subDays(45),
                'end_date' => now()->addDays(5),
                'status' => 'in_progress',
                'created_at' => now()->subDays(45),
                'updated_at' => now(),
            ],
            [
                'name' => 'Sistema de Reservas',
                'description' => 'Plataforma web para gestionar reservas de restaurantes, hoteles y servicios, con calendario interactivo y notificaciones automáticas.',
                'user_id' => $users->random()->id,
                'progress' => 0.00,
                'start_date' => now()->addDays(10),
                'end_date' => now()->addDays(70),
                'status' => 'pending',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Migración de Base de Datos',
                'description' => 'Migración completa de base de datos legacy a PostgreSQL con optimización de consultas y mejora de rendimiento.',
                'user_id' => $users->random()->id,
                'progress' => 0.00,
                'start_date' => now()->subDays(5),
                'end_date' => now()->subDays(2),
                'status' => 'cancelled',
                'created_at' => now()->subDays(10),
                'updated_at' => now()->subDays(2),
            ],
            [
                'name' => 'Portal de Recursos Humanos',
                'description' => 'Sistema integral de RRHH con gestión de empleados, nóminas, vacaciones, evaluaciones de desempeño y autoservicio.',
                'user_id' => $users->random()->id,
                'progress' => 50.75,
                'start_date' => now()->subDays(20),
                'end_date' => now()->addDays(25),
                'status' => 'in_progress',
                'created_at' => now()->subDays(20),
                'updated_at' => now(),
            ],
            [
                'name' => 'Integración con ERP',
                'description' => 'Desarrollo de conectores y APIs para integrar sistemas existentes con SAP ERP, sincronización de datos en tiempo real.',
                'user_id' => $users->random()->id,
                'progress' => 25.00,
                'start_date' => now()->subDays(10),
                'end_date' => now()->addDays(35),
                'status' => 'in_progress',
                'created_at' => now()->subDays(10),
                'updated_at' => now(),
            ],
            [
                'name' => 'Refactoring Frontend',
                'description' => 'Modernización completa del frontend legacy a Vue.js 3 con TypeScript, mejoras de rendimiento y diseño responsive.',
                'user_id' => $users->random()->id,
                'progress' => 100.00,
                'start_date' => now()->subDays(60),
                'end_date' => now()->subDays(5),
                'status' => 'completed',
                'created_at' => now()->subDays(60),
                'updated_at' => now()->subDays(5),
            ],
        ];

        foreach ($projects as $project) {
            Project::create($project);
        }

        $this->command->info('✅ Proyectos creados exitosamente.');
        $this->command->info('📊 Total: ' . count($projects) . ' proyectos');
        $this->command->info('🟡 Pendientes: ' . collect($projects)->where('status', 'pending')->count());
        $this->command->info('🔵 En progreso: ' . collect($projects)->where('status', 'in_progress')->count());
        $this->command->info('🟢 Completados: ' . collect($projects)->where('status', 'completed')->count());
        $this->command->info('🔴 Cancelados: ' . collect($projects)->where('status', 'cancelled')->count());
    }
}
