<?php

use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\TaskController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware(['auth:sanctum'])->group(function () {
    // Rutas de proyectos
    Route::apiResource('projects', ProjectController::class);

    // Rutas de tareas
    Route::apiResource('tasks', TaskController::class);
});


