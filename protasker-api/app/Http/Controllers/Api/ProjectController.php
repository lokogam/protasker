<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use App\Http\Resources\ProjectResource;
use App\Services\ProjectService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ProjectController extends Controller
{
    public function __construct(
        private ProjectService $projectService
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $user = $request->user();
        $filters = $request->only(['status']);

        // Si es desarrollador, filtrar solo sus proyectos
        if ($user->hasRole('desarrollador')) {
            $filters['user_id'] = $user->id;
        }

        $projects = $this->projectService->getAllProjects($filters);

        return ProjectResource::collection($projects);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProjectRequest $request): ProjectResource
    {
        $project = $this->projectService->createProject(
            $request->validated(),
            $request->user()
        );

        return new ProjectResource($project);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, string $id): ProjectResource|JsonResponse
    {
        $project = $this->projectService->findProject((int) $id);

        if (!$project) {
            return response()->json([
                'message' => 'Proyecto no encontrado.'
            ], 404);
        }

        // Verificar permisos
        if (!$this->projectService->canUserAccessProject($request->user(), $project)) {
            return response()->json([
                'message' => 'No tienes permisos para acceder a este proyecto.'
            ], 403);
        }

        return new ProjectResource($project);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProjectRequest $request, string $id): ProjectResource|JsonResponse
    {
        $project = $this->projectService->findProject((int) $id);

        if (!$project) {
            return response()->json([
                'message' => 'Proyecto no encontrado.'
            ], 404);
        }

        // Verificar permisos
        if (!$this->projectService->canUserAccessProject($request->user(), $project)) {
            return response()->json([
                'message' => 'No tienes permisos para modificar este proyecto.'
            ], 403);
        }

        $updatedProject = $this->projectService->updateProject(
            $project,
            $request->validated()
        );

        return new ProjectResource($updatedProject);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $id): JsonResponse
    {
        $project = $this->projectService->findProject((int) $id);

        if (!$project) {
            return response()->json([
                'message' => 'Proyecto no encontrado.'
            ], 404);
        }

        // Verificar permisos
        if (!$this->projectService->canUserAccessProject($request->user(), $project)) {
            return response()->json([
                'message' => 'No tienes permisos para eliminar este proyecto.'
            ], 403);
        }

        $this->projectService->deleteProject($project);

        return response()->json([
            'message' => 'Proyecto eliminado exitosamente.'
        ]);
    }
}
