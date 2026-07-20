<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HealthController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Public\CinemaController;
use App\Http\Controllers\User\UserProfileController;

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

// Authentication routes (no middleware)
Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
});

// Public API routes (no authentication required)
Route::prefix('cinemas')->group(function () {
    Route::get('/', [CinemaController::class, 'api_index']);
    Route::get('/{uuid}', [CinemaController::class, 'api_show']);
});

// Health Check routes (no authentication required)
Route::prefix('health')->group(function () {
    Route::get('/', [HealthController::class, 'simple']);
    Route::get('/full', [HealthController::class, 'index']);
    Route::get('/system', [HealthController::class, 'system']);
    Route::get('/database', [HealthController::class, 'database']);
    Route::get('/redis', [HealthController::class, 'redis']);
    Route::get('/mongodb', [HealthController::class, 'mongodb']);
});

// Protected routes (require authentication)
Route::middleware('auth:api')->group(function () {
    Route::prefix('auth')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/user', [AuthController::class, 'user']);
    });

    Route::prefix('user')->group(function () {
        Route::get('/profile', [UserProfileController::class, 'profile']);
    });
});
