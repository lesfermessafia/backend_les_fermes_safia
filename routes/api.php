<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:api')->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/refresh', [AuthController::class, 'refresh']);
    
    Route::middleware('admin')->group(function () {
        Route::post('/register', [AuthController::class, 'register']);
        Route::get('/users/{id}', [AuthController::class, 'getUserById']);
        Route::put('/users/{id}/block', [AuthController::class, 'blockUser']);
        Route::get('/users/non-admin', [AuthController::class, 'getUsersNonAdmin']);
    });
});
