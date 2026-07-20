<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Employee\FilmController;
use App\Http\Controllers\Employee\SeanceController;
use App\Http\Controllers\Employee\DashboardController;
use App\Http\Controllers\Employee\ReservationController;

// Routes employés
Route::middleware(['auth', 'verified'])
    ->prefix('employee')
    ->name('employee.')
    ->group(function () {

        // Dashboard employé
        Route::get('/dashboard', DashboardController::class)->name('dashboard');

        // Séances
        Route::get('/seances', [SeanceController::class, 'index'])->name('seances.index');

        // Réservations
        Route::get('/reservations', [ReservationController::class, 'index'])->name('reservations.index');

        // Films
        Route::get('/films', [FilmController::class, 'index'])->name('films.index');

    });
