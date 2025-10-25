<?php

namespace App\Services;

use App\Models\Task;
use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class TaskService
{
    /**
     * Get all tasks with optional filters
     */
    public function getAllTasks(array $filters = []): LengthAwarePaginator
    {
        $query = Task::with(['project', 'assignedUser']);

        // Filtrar por proyecto
        if (isset($filters['project_id'])) {
            $query->where('project_id', $filters['project_id']);
        }

        // Filtrar por estado
        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        // Filtrar por usuario asignado
        if (isset($filters['assigned_to'])) {
            $query->where('assigned_to', $filters['assigned_to']);
        }

        return $query->paginate(15);
    }

    /**
     * Get tasks for a specific user
     */
    public function getUserTasks(User $user): Collection
    {
        return Task::with(['project'])
            ->where('assigned_to', $user->id)
            ->get();
    }

    /**
     * Get tasks for a specific project
     */
    public function getProjectTasks(Project $project): Collection
    {
        return Task::with(['assignedUser'])
            ->where('project_id', $project->id)
            ->get();
    }

    /**
     * Create a new task
     */
    public function createTask(array $data): Task
    {
        $task = Task::create($data);

        // Actualizar progreso del proyecto
        $this->updateProjectProgress($task->project);

        return $task->load(['project', 'assignedUser']);
    }

    /**
     * Find task by ID
     */
    public function findTask(int $id): ?Task
    {
        return Task::with(['project', 'assignedUser'])->find($id);
    }

    /**
     * Update task
     */
    public function updateTask(Task $task, array $data): Task
    {
        $oldStatus = $task->status;
        $task->update($data);

        // Si cambió el estado o el proyecto, recalcular progreso
        if ($oldStatus !== $task->status || isset($data['project_id'])) {
            $this->updateProjectProgress($task->project);

            // Si cambió de proyecto, actualizar el anterior también
            if (isset($data['project_id']) && $data['project_id'] !== $task->project_id) {
                $oldProject = Project::find($task->getOriginal('project_id'));
                if ($oldProject) {
                    $this->updateProjectProgress($oldProject);
                }
            }
        }

        return $task->fresh(['project', 'assignedUser']);
    }

    /**
     * Delete task
     */
    public function deleteTask(Task $task): bool
    {
        $project = $task->project;
        $deleted = $task->delete();

        if ($deleted) {
            // Actualizar progreso del proyecto
            $this->updateProjectProgress($project);
        }

        return $deleted;
    }

    /**
     * Update project progress based on completed tasks
     */
    private function updateProjectProgress(Project $project): void
    {
        $totalTasks = $project->tasks()->count();

        if ($totalTasks === 0) {
            $project->update(['progress' => 0.00]);
            return;
        }

        $completedTasks = $project->tasks()->where('status', 'completed')->count();
        $progress = ($completedTasks / $totalTasks) * 100;

        $project->update(['progress' => round($progress, 2)]);
    }

    /**
     * Check if user can access task
     */
    public function canUserAccessTask(User $user, Task $task): bool
    {
        // Admin puede ver todas las tareas
        if ($user->hasRole('administrador')) {
            return true;
        }

        // Desarrollador puede ver tareas de sus proyectos o tareas asignadas a él
        return $task->project->user_id === $user->id || $task->assigned_to === $user->id;
    }

    /**
     * Check if user can modify task
     */
    public function canUserModifyTask(User $user, Task $task): bool
    {
        // Admin puede modificar todas las tareas
        if ($user->hasRole('administrador')) {
            return true;
        }

        // Desarrollador solo puede modificar tareas de sus propios proyectos
        return $task->project->user_id === $user->id;
    }
}
