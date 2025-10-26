<?php

namespace Database\Seeders;

use App\Models\Task;
use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Seeder;

class TaskSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $projects = Project::all();
        $users = User::all();

        if ($projects->isEmpty()) {
            $this->command->info('No hay proyectos disponibles. Ejecuta ProjectSeeder primero.');
            return;
        }

        if ($users->isEmpty()) {
            $this->command->info('No hay usuarios disponibles. Ejecuta UserSeeder primero.');
            return;
        }

        $tasksTemplate = [
            // Tareas de análisis y planificación
            [
                'name' => 'Análisis de Requerimientos',
                'description' => 'Reunir y documentar todos los requerimientos funcionales y no funcionales del proyecto.',
                'status' => 'completed',
                'percentage' => 15.00,
                'due_date' => now()->addDays(7),
            ],
            [
                'name' => 'Diseño de Base de Datos',
                'description' => 'Crear el modelo entidad-relación y diseñar la estructura de la base de datos.',
                'status' => 'completed',
                'percentage' => 20.00,
                'due_date' => now()->addDays(10),
            ],
            [
                'name' => 'Arquitectura del Sistema',
                'description' => 'Definir la arquitectura técnica y los patrones de diseño a utilizar.',
                'status' => 'in_progress',
                'percentage' => 10.00,
                'due_date' => now()->addDays(12),
            ],

            // Tareas de desarrollo backend
            [
                'name' => 'Configuración del Entorno',
                'description' => 'Configurar el entorno de desarrollo, dependencias y herramientas necesarias.',
                'status' => 'completed',
                'percentage' => 5.00,
                'due_date' => now()->addDays(3),
            ],
            [
                'name' => 'Desarrollo de APIs',
                'description' => 'Implementar los endpoints REST para la comunicación frontend-backend.',
                'status' => 'in_progress',
                'percentage' => 25.00,
                'due_date' => now()->addDays(20),
            ],
            [
                'name' => 'Sistema de Autenticación',
                'description' => 'Implementar el sistema de login, registro y gestión de sesiones.',
                'status' => 'pending',
                'percentage' => 15.00,
                'due_date' => now()->addDays(15),
            ],

            // Tareas de frontend
            [
                'name' => 'Diseño de Interfaz',
                'description' => 'Crear mockups y prototipos de la interfaz de usuario.',
                'status' => 'completed',
                'percentage' => 8.00,
                'due_date' => now()->addDays(8),
            ],
            [
                'name' => 'Desarrollo de Componentes',
                'description' => 'Desarrollar los componentes reutilizables de la interfaz.',
                'status' => 'in_progress',
                'percentage' => 20.00,
                'due_date' => now()->addDays(25),
            ],
            [
                'name' => 'Integración Frontend-Backend',
                'description' => 'Conectar la interfaz con las APIs del backend.',
                'status' => 'pending',
                'percentage' => 12.00,
                'due_date' => now()->addDays(30),
            ],

            // Tareas de testing y calidad
            [
                'name' => 'Testing Unitario',
                'description' => 'Escribir y ejecutar tests unitarios para todas las funcionalidades.',
                'status' => 'pending',
                'percentage' => 10.00,
                'due_date' => now()->addDays(35),
            ],
            [
                'name' => 'Testing de Integración',
                'description' => 'Realizar pruebas de integración entre módulos y sistemas.',
                'status' => 'pending',
                'percentage' => 8.00,
                'due_date' => now()->addDays(40),
            ],
            [
                'name' => 'Testing de Usuario',
                'description' => 'Ejecutar pruebas de aceptación y usabilidad con usuarios finales.',
                'status' => 'pending',
                'percentage' => 5.00,
                'due_date' => now()->addDays(45),
            ],

            // Tareas de despliegue
            [
                'name' => 'Configuración de Servidor',
                'description' => 'Configurar el servidor de producción y los servicios necesarios.',
                'status' => 'pending',
                'percentage' => 7.00,
                'due_date' => now()->addDays(50),
            ],
            [
                'name' => 'Despliegue a Producción',
                'description' => 'Realizar el despliegue final del sistema en el ambiente de producción.',
                'status' => 'pending',
                'percentage' => 10.00,
                'due_date' => now()->addDays(55),
            ],
            [
                'name' => 'Documentación Final',
                'description' => 'Completar la documentación técnica y de usuario del sistema.',
                'status' => 'pending',
                'percentage' => 5.00,
                'due_date' => now()->addDays(60),
            ],
        ];

        $totalTasks = 0;

        foreach ($projects as $project) {
            // Determinar cuántas tareas crear para este proyecto (entre 3 y 8)
            $numTasks = rand(3, 8);

            // Seleccionar tareas aleatoriamente del template
            $selectedTasks = collect($tasksTemplate)->random($numTasks);

            // Ajustar porcentajes para que sumen exactamente 100%
            $totalPercentage = $selectedTasks->sum('percentage');
            $adjustmentFactor = 100 / $totalPercentage;

            foreach ($selectedTasks as $taskTemplate) {
                $adjustedPercentage = round($taskTemplate['percentage'] * $adjustmentFactor, 2);

                Task::create([
                    'name' => $taskTemplate['name'],
                    'description' => $taskTemplate['description'],
                    'project_id' => $project->id,
                    'assigned_to' => $users->random()->id,
                    'status' => $taskTemplate['status'],
                    'percentage' => $adjustedPercentage,
                    'due_date' => $taskTemplate['due_date'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $totalTasks++;
            }
        }

        // Actualizar progreso de proyectos basado en tareas completadas
        foreach ($projects as $project) {
            $projectTasks = $project->tasks;
            $completedTasks = $projectTasks->where('status', 'completed');

            if ($projectTasks->count() > 0) {
                $progress = $completedTasks->sum('percentage');
                $project->update(['progress' => round($progress, 2)]);
            }
        }

        $this->command->info('✅ Tareas creadas exitosamente.');
        $this->command->info('📋 Total: ' . $totalTasks . ' tareas');
        $this->command->info('🟡 Pendientes: ' . Task::where('status', 'pending')->count());
        $this->command->info('🔵 En progreso: ' . Task::where('status', 'in_progress')->count());
        $this->command->info('🟢 Completadas: ' . Task::where('status', 'completed')->count());
        $this->command->info('📊 Progreso de proyectos actualizado automáticamente.');
    }
}
