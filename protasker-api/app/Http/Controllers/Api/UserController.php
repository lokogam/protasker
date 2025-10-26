<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class UserController extends Controller
{
    /**
     * Display a listing of users
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        // Solo administradores pueden ver la lista de usuarios
        if (!$user->hasRole('administrador')) {
            return response()->json([
                'message' => 'No autorizado'
            ], 403);
        }

        $query = User::with('roles');

        // Filtrar por rol si se especifica
        if ($request->has('role')) {
            $query->whereHas('roles', function ($q) use ($request) {
                $q->where('name', $request->role);
            });
        }

        $users = $query->get();

        return response()->json([
            'data' => $users->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'roles' => $user->roles->map(function ($role) {
                        return [
                            'id' => $role->id,
                            'name' => $role->name,
                        ];
                    }),
                    'created_at' => $user->created_at?->format('Y-m-d H:i:s'),
                ];
            })
        ]);
    }

    /**
     * Display the specified user
     */
    public function show(Request $request, User $user): JsonResponse
    {
        $currentUser = $request->user();

        // Solo administradores pueden ver otros usuarios
        if (!$currentUser->hasRole('administrador') && $currentUser->id !== $user->id) {
            return response()->json([
                'message' => 'No autorizado'
            ], 403);
        }

        $user->load('roles');

        return response()->json([
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'roles' => $user->roles->map(function ($role) {
                    return [
                        'id' => $role->id,
                        'name' => $role->name,
                    ];
                }),
                'created_at' => $user->created_at?->format('Y-m-d H:i:s'),
            ]
        ]);
    }

    /**
     * Search users by name or email
     */
    public function search(Request $request): JsonResponse
    {
        $user = $request->user();

        // Solo administradores pueden buscar usuarios
        if (!$user->hasRole('administrador')) {
            return response()->json([
                'message' => 'No autorizado'
            ], 403);
        }

        $query = $request->get('query', '');
        $limit = min($request->get('limit', 10), 50); // Máximo 50 resultados

        $users = User::with('roles')
            ->where(function ($q) use ($query) {
                if ($query) {
                    $q->where('name', 'LIKE', "%{$query}%")
                      ->orWhere('email', 'LIKE', "%{$query}%");
                }
            })
            ->limit($limit)
            ->get();

        return response()->json([
            'data' => $users->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'roles' => $user->roles->map(function ($role) {
                        return [
                            'id' => $role->id,
                            'name' => $role->name,
                        ];
                    }),
                    'created_at' => $user->created_at?->format('Y-m-d H:i:s'),
                ];
            })
        ]);
    }

    /**
     * Get users available for task assignment (accessible by developers)
     */
    public function getAvailableUsers(Request $request): JsonResponse
    {
        // Tanto administradores como desarrolladores pueden ver usuarios para asignación
        $users = User::with('roles')->get();

        return response()->json([
            'data' => $users->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'roles' => $user->roles->map(function ($role) {
                        return [
                            'id' => $role->id,
                            'name' => $role->name,
                        ];
                    }),
                ];
            })
        ]);
    }
}
