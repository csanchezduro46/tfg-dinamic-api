<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

// User login
Route::prefix('oauth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
});

// Authenticated with user group
Route::prefix('oauth')->middleware(['auth:sanctum'])->group(function () {
    Route::get('/me', [AuthController::class, 'getAccount']);
    Route::get('/logout', [AuthController::class, 'logout']);
});

// Authenticated with admin group
Route::prefix('oauth')->middleware(['auth:sanctum', 'role:admin', 'verified'])->group(function () {
    Route::post('/signup', [AuthController::class, 'signUp']);
});
