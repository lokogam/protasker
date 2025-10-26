<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\TaskController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Auth\RegisteredUserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Rutas de autenticación (públicas)
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [RegisteredUserController::class, 'store']);

Route::middleware(['auth:sanctum'])->group(function () {
    // Rutas de autenticación protegidas
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);

    // Rutas de usuarios
    Route::get('/users/search', [UserController::class, 'search']); // Buscar usuarios
    Route::get('/users/available', [UserController::class, 'getAvailableUsers']); // Usuarios disponibles para asignación
    Route::get('/users', [UserController::class, 'index']);
    Route::get('/users/{user}', [UserController::class, 'show']);

    // Rutas de proyectos
    Route::apiResource('projects', ProjectController::class);

    // Rutas de tareas
    Route::get('/tasks/all', [TaskController::class, 'all']); // Nueva ruta para todas las tareas
    Route::apiResource('tasks', TaskController::class);
});
