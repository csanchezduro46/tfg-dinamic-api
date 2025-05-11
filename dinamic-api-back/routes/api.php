<?php

use App\Http\Controllers\ApiCalls\ApiCallMappingController;
use App\Http\Controllers\ApiCalls\ApiCallMappingFieldController;
use App\Http\Controllers\DatabaseConnections\DatabaseConnectionController;
use App\Http\Controllers\DatabaseConnections\DatabaseSchemaController;
use App\Http\Controllers\Platforms\ApiGroupController;
use App\Http\Controllers\Platforms\PlatformConnectionController;
use App\Http\Controllers\Platforms\PlatformConnectionCredentialsController;
use App\Http\Controllers\Platforms\PlatformController;
use App\Http\Controllers\Platforms\PlatformNecessaryKeysController;
use App\Http\Controllers\Platforms\PlatformVersionController;
use App\Http\Controllers\ApiCalls\ApiCallController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    // Group
    Route::get('/groups', [ApiGroupController::class, 'getAll']);

    Route::middleware('role:admin')->group(function () {
        Route::post('/groups', [ApiGroupController::class, 'store']);
        Route::put('/groups/{id}', [ApiGroupController::class, 'update']);
        Route::delete('/groups/{id}', [ApiGroupController::class, 'delete']);
    });

    // Plataformas
    Route::get('/platforms', [PlatformController::class, 'getPlatformsSearch']);

    Route::middleware('role:admin')->group(function () {
        Route::post('/platforms', [PlatformController::class, 'store']);
        Route::put('/platforms/{id}', [PlatformController::class, 'update']);
        Route::delete('/platforms/{id}', [PlatformController::class, 'delete']);
    });

    // Necessary Keys
    Route::get('/platforms/{id}/necessary-keys', [PlatformNecessaryKeysController::class, 'getKeysPlatform']);

    Route::middleware('role:admin')->group(function () {
        Route::post('/platforms/{id}/necessary-keys', [PlatformNecessaryKeysController::class, 'store']);
        Route::put('/platforms/{id}/necessary-keys/{key}', [PlatformNecessaryKeysController::class, 'update']);
        Route::delete('/platforms/{id}/necessary-keys/{key}', [PlatformNecessaryKeysController::class, 'delete']);
    });

    // Versiones
    Route::get('/platforms/{id}/versions', [PlatformVersionController::class, 'getByPlatform']);

    Route::middleware('role:admin')->group(function () {
        Route::get('/versions', [PlatformVersionController::class, 'getAll']);
        Route::post('/platforms/{id}/versions', [PlatformVersionController::class, 'store']);
        Route::put('/versions/{id}', [PlatformVersionController::class, 'update']);
        Route::delete('/versions/{id}', [PlatformVersionController::class, 'delete']);
    });

    // Conexiones de usuarios a plataformas externas
    Route::middleware('role:admin')->group(function () {
        Route::get('/connections', [PlatformConnectionController::class, 'getAll']);
    });

    Route::get('/connections/me', [PlatformConnectionController::class, 'getByUser']);
    Route::post('/connections', [PlatformConnectionController::class, 'store']);
    Route::get('/connections/{id}', [PlatformConnectionController::class, 'getSingle']);
    Route::put('/connections/{id}', [PlatformConnectionController::class, 'update']);
    Route::delete('/connections/{id}', [PlatformConnectionController::class, 'delete']);

    // Platform connection credentials
    Route::get('/connections/{id}/test', [PlatformConnectionCredentialsController::class, 'testConnection']);
    Route::get('/connections/{id}/credentials', [PlatformConnectionCredentialsController::class, 'getByConnection']);
    Route::post('/connections/{id}/credentials', [PlatformConnectionCredentialsController::class, 'storeKeys']);
    Route::delete('/connections/{id}/credentials', [PlatformConnectionCredentialsController::class, 'deleteAllKey']);
    Route::delete('/connections/{id}/credentials/{idKey}', [PlatformConnectionCredentialsController::class, 'deleteKey']);
});

Route::prefix('db-connections')->middleware('auth:sanctum')->group(function () {
    Route::middleware('role:admin')->group(function () {
        Route::get('/', [DatabaseConnectionController::class, 'getAll']);
    });
    Route::get('/me', [DatabaseConnectionController::class, 'getConnectionsUser']);
    Route::get('/{id}', [DatabaseConnectionController::class, 'getConnectionBd']);
    Route::post('/', [DatabaseConnectionController::class, 'store']);
    Route::put('/{id}', [DatabaseConnectionController::class, 'update']);
    Route::delete('/{id}', [DatabaseConnectionController::class, 'delete']);
    Route::get('/{id}/test', [DatabaseConnectionController::class, 'testConnection']);

    Route::get('{id}/schema', [DatabaseSchemaController::class, 'getFullSchema']);
    Route::get('{id}/tables', [DatabaseSchemaController::class, 'getTables']);
    Route::get('{id}/tables/{table}/columns', [DatabaseSchemaController::class, 'getColumns']);
});

Route::prefix('api-calls')->middleware('auth:sanctum')->group(function () {
    Route::get('/{id}', [ApiCallController::class, 'get']);
    Route::get('/platform-version/{versionId}', [ApiCallController::class, 'getByPlatformVersion']);
    Route::get('/{id}/fields', [ApiCallController::class, 'getFields']);

    Route::middleware('role:admin')->group(function () {
        Route::get('/', [ApiCallController::class, 'getAll']);
        Route::post('/', [ApiCallController::class, 'store']);
        Route::put('/{id}', [ApiCallController::class, 'update']);
        Route::delete('/{id}', [ApiCallController::class, 'delete']);
    });
});

Route::prefix('mappings')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [ApiCallMappingController::class, 'getMappings']);
    Route::post('/', [ApiCallMappingController::class, 'store']);
    Route::put('/{id}', [ApiCallMappingController::class, 'update']);
    Route::delete('/{id}', [ApiCallMappingController::class, 'delete']);
    Route::get('/{id}/fields', [ApiCallMappingFieldController::class, 'getFieldsByMapping']);
    Route::post('/{id}/fields', [ApiCallMappingFieldController::class, 'store']);
    Route::put('/{id}/fields/{fieldId}', [ApiCallMappingFieldController::class, 'update']);
    Route::delete('/{id}/fields/{fieldId}', [ApiCallMappingFieldController::class, 'delete']);
});
