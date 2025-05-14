<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\VerificationController;
use Illuminate\Support\Facades\Route;

// User login
Route::middleware(['verified'])->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
});
// Recuperación de contraseña
Route::post('/password/forgot', [PasswordController::class, 'sendResetLinkEmail']);
Route::post('/password/reset', [PasswordController::class, 'reset']);

Route::middleware(['auth:sanctum', 'verified'])->group(function () {
    Route::get('/me', [AuthController::class, 'getAccount']);
    Route::get('/logout', [AuthController::class, 'logout']);
});

Route::post('/signup', [AuthController::class, 'signUp']);

// Verificación de email usuario
Route::get('/email/verify/{id}/{hash}', [VerificationController::class, 'verify'])
    ->name('verification.verify');

Route::post('/email/verification-resend', [VerificationController::class, 'resend'])
    ->middleware('throttle:30,1')
    ->name('verification.resend');