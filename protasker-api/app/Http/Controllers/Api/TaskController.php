<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Http\Resources\TaskResource;
use App\Services\TaskService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class TaskController extends Controller
{
    public function __construct(
        private TaskService $taskService
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $user = $request->user();
        $filters = $request->only(['project_id', 'status', 'assigned_to']);

        // Si es desarrollador, filtrar solo tareas que puede ver
        if ($user->hasRole('desarrollador')) {
            // No agregamos filtro automático aquí, se verifica en el servicio
        }

        $tasks = $this->taskService->getAllTasks($filters);

        // Filtrar tareas por permisos después de la consulta
        if ($user->hasRole('desarrollador')) {
            $tasks->getCollection()->transform(function ($task) use ($user) {
                return $this->taskService->canUserAccessTask($user, $task) ? $task : null;
            })->filter();
        }

        return TaskResource::collection($tasks);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTaskRequest $request): TaskResource|JsonResponse
    {
        $data = $request->validated();

        // Verificar permisos sobre el proyecto
        $user = $request->user();
        $project = \App\Models\Project::find($data['project_id']);

        if (!$project) {
            return response()->json([
                'message' => 'Proyecto no encontrado.'
            ], 404);
        }

        // Solo admin o dueño del proyecto puede crear tareas
        if ($user->hasRole('desarrollador') && $project->user_id !== $user->id) {
            return response()->json([
                'message' => 'No tienes permisos para crear tareas en este proyecto.'
            ], 403);
        }

        $task = $this->taskService->createTask($data);

        return new TaskResource($task);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, string $id): TaskResource|JsonResponse
    {
        $task = $this->taskService->findTask((int) $id);

        if (!$task) {
            return response()->json([
                'message' => 'Tarea no encontrada.'
            ], 404);
        }

        // Verificar permisos
        if (!$this->taskService->canUserAccessTask($request->user(), $task)) {
            return response()->json([
                'message' => 'No tienes permisos para acceder a esta tarea.'
            ], 403);
        }

        return new TaskResource($task);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTaskRequest $request, string $id): TaskResource|JsonResponse
    {
        $task = $this->taskService->findTask((int) $id);

        if (!$task) {
            return response()->json([
                'message' => 'Tarea no encontrada.'
            ], 404);
        }

        // Verificar permisos
        if (!$this->taskService->canUserModifyTask($request->user(), $task)) {
            return response()->json([
                'message' => 'No tienes permisos para modificar esta tarea.'
            ], 403);
        }

        $updatedTask = $this->taskService->updateTask(
            $task,
            $request->validated()
        );

        return new TaskResource($updatedTask);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $id): JsonResponse
    {
        $task = $this->taskService->findTask((int) $id);

        if (!$task) {
            return response()->json([
                'message' => 'Tarea no encontrada.'
            ], 404);
        }

        // Verificar permisos
        if (!$this->taskService->canUserModifyTask($request->user(), $task)) {
            return response()->json([
                'message' => 'No tienes permisos para eliminar esta tarea.'
            ], 403);
        }

        $this->taskService->deleteTask($task);

        return response()->json([
            'message' => 'Tarea eliminada exitosamente.'
        ]);
    }
}
