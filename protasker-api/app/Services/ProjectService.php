<?php

namespace App\Services;

use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class ProjectService
{
    /**
     * Get all projects with optional filters
     */
    public function getAllProjects(array $filters = []): LengthAwarePaginator
    {
        $query = Project::with(['user', 'tasks']);

        // Filtrar por usuario si es desarrollador
        if (isset($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        // Filtrar por estado
        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->paginate(15);
    }

    /**
     * Get projects for a specific user
     */
    public function getUserProjects(User $user): Collection
    {
        return Project::with(['tasks'])
            ->where('user_id', $user->id)
            ->get();
    }

    /**
     * Create a new project
     */
    public function createProject(array $data, User $user): Project
    {
        $data['user_id'] = $user->id;
        $data['progress'] = 0.00; // Iniciar con 0% de progreso

        return Project::create($data);
    }

    /**
     * Find project by ID
     */
    public function findProject(int $id): ?Project
    {
        return Project::with(['user', 'tasks'])->find($id);
    }

    /**
     * Update project
     */
    public function updateProject(Project $project, array $data): Project
    {
        $project->update($data);

        // Recalcular progreso si hay tareas
        $this->updateProjectProgress($project);

        return $project->fresh(['user', 'tasks']);
    }

    /**
     * Delete project
     */
    public function deleteProject(Project $project): bool
    {
        return $project->delete();
    }

    /**
     * Update project progress based on completed tasks
     */
    public function updateProjectProgress(Project $project): void
    {
        $totalTasks = $project->tasks()->count();

        if ($totalTasks === 0) {
            $project->update(['progress' => 0.00]);
            return;
        }

        $completedTasks = $project->tasks()->where('status', 'completed')->count();
        $progress = ($completedTasks / $totalTasks) * 100;

        $project->update(['progress' => round($progress, 2)]);
    }    /**
     * Check if user can access project
     */
    public function canUserAccessProject(User $user, Project $project): bool
    {
        // Admin puede ver todos los proyectos
        if ($user->hasRole('administrador')) {
            return true;
        }

        // Desarrollador solo puede ver sus propios proyectos
        return $project->user_id === $user->id;
    }
}
