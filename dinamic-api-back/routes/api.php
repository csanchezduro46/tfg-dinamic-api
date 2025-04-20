<?php

use App\Http\Controllers\Platforms\ApiCategoryController;
use App\Http\Controllers\Platforms\PlatformController;
use App\Http\Controllers\Platforms\PlatformVersionController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    // Categorías
    Route::get('/categories', [ApiCategoryController::class, 'getAll']);

    Route::middleware('role:admin')->group(function () {
        Route::post('/categories', [ApiCategoryController::class, 'store']);
        Route::put('/categories/{id}', [ApiCategoryController::class, 'update']);
        Route::delete('/categories/{id}', [ApiCategoryController::class, 'delete']);
    });

    // Plataformas
    Route::get('/platforms', [PlatformController::class, 'getPlatformsSearch']);

    Route::middleware('role:admin')->group(function () {
        Route::post('/platforms', [PlatformController::class, 'store']);
        Route::put('/platforms/{id}', [PlatformController::class, 'update']);
        Route::delete('/platforms/{id}', [PlatformController::class, 'delete']);
    });

    // Versiones
    Route::get('/platforms/{id}/versions', [PlatformVersionController::class, 'getByPlatform']);

    Route::middleware('role:admin')->group(function () {
        Route::get('/versions', [PlatformVersionController::class, 'getAll']);
        Route::post('/platforms/{id}/versions', [PlatformVersionController::class, 'store']);
        Route::put('/versions/{id}', [PlatformVersionController::class, 'update']);
        Route::delete('/versions/{id}', [PlatformVersionController::class, 'delete']);
    });
});