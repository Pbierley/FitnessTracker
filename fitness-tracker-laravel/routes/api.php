<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\WorkoutController;
use App\Http\Controllers\Api\SessionController;
use App\Http\Controllers\Api\WeightController;

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

// Public auth routes
Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
});

// Protected auth routes
Route::middleware('custom.auth')->prefix('auth')->group(function () {
    Route::get('/verify', [AuthController::class, 'verify']);
    Route::post('/logout', [AuthController::class, 'logout']);
});

// Protected API routes
Route::middleware('custom.auth')->group(function () {
    
    // Workout routes
    Route::prefix('workouts')->group(function () {
        Route::get('/', [WorkoutController::class, 'index']);
        Route::get('/{id}', [WorkoutController::class, 'show']);
        Route::post('/', [WorkoutController::class, 'store']);
        Route::put('/{id}', [WorkoutController::class, 'update']);
        Route::delete('/{id}', [WorkoutController::class, 'destroy']);
    });
    
    // Session routes
    Route::prefix('sessions')->group(function () {
        Route::get('/', [SessionController::class, 'index']);
        Route::get('/{id}', [SessionController::class, 'show']);
        Route::post('/', [SessionController::class, 'store']);
        Route::put('/{id}', [SessionController::class, 'update']);
        Route::delete('/{id}', [SessionController::class, 'destroy']);
    });
    
    // Weight tracking routes
    Route::prefix('weight')->group(function () {
        Route::get('/', [WeightController::class, 'index']);
        Route::get('/{id}', [WeightController::class, 'show']);
        Route::post('/', [WeightController::class, 'store']);
        Route::put('/{id}', [WeightController::class, 'update']);
        Route::delete('/{id}', [WeightController::class, 'destroy']);
    });
});

