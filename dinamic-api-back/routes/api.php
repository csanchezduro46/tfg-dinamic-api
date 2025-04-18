<?php

use Illuminate\Support\Facades\Route;

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


// Authenticated with user group
Route::group(['middleware' => ['auth:api', 'role:user']], function() {
});

// Authenticated with admin group
Route::group(['middleware' => ['auth:api', 'role:admin']], function() {
});
