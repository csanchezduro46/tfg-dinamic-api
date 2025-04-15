<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Llamadas anónimas

// User login
Route::post('/login', [AuthController::class, 'login']);

// Authenticated with user group
Route::group(['middleware' => ['auth:api', 'role:user']], function() {
    Route::get('/me', [AuthController::class, 'getAccount']);
    Route::get('/logout', [AuthController::class, 'logout']);
});

// Authenticated with admin group
Route::group(['middleware' => ['auth:api', 'role:admin']], function() {
    Route::post('/signup', [AuthController::class, 'signUp']);
});
