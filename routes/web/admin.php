<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
// Admin Dashboard Controller
use App\Http\Controllers\Admin\AdminDashboardController;
// Reservation Controllers
use App\Http\Controllers\Admin\Reservation\ShowReservationController;
use App\Http\Controllers\Admin\Reservation\ListReservationsController;
// Film Controllers
use App\Http\Controllers\Admin\Film\EditFilmController as AdminEditFilmController;
use App\Http\Controllers\Admin\Film\ShowFilmController as AdminShowFilmController;
use App\Http\Controllers\Admin\Film\ListFilmsController as AdminListFilmsController;
use App\Http\Controllers\Admin\Film\StoreFilmController as AdminStoreFilmController;
use App\Http\Controllers\Admin\Film\CreateFilmController as AdminCreateFilmController;
use App\Http\Controllers\Admin\Film\UpdateFilmController as AdminUpdateFilmController;
// Cinema Controllers
use App\Http\Controllers\Admin\Users\EditClientController as AdminEditClientController;
use App\Http\Controllers\Admin\Users\ShowClientController as AdminShowClientController;
use App\Http\Controllers\Admin\Cinema\EditCinemaController as AdminEditCinemaController;
use App\Http\Controllers\Admin\Cinema\ShowCinemaController as AdminShowCinemaController;
use App\Http\Controllers\Admin\Users\ListClientsController as AdminListClientsController;
use App\Http\Controllers\Admin\Cinema\ListCinemasController as AdminListCinemasController;
use App\Http\Controllers\Admin\Cinema\StoreCinemaController as AdminStoreCinemaController;
use App\Http\Controllers\Admin\Users\EditEmployeeController as AdminEditEmployeeController;
use App\Http\Controllers\Admin\Users\ShowEmployeeController as AdminShowEmployeeController;
use App\Http\Controllers\Admin\Users\UpdateClientController as AdminUpdateClientController;
// User Controllers
use App\Http\Controllers\Admin\Cinema\CreateCinemaController as AdminCreateCinemaController;
use App\Http\Controllers\Admin\Cinema\UpdateCinemaController as AdminUpdateCinemaController;
use App\Http\Controllers\Admin\Users\ListEmployeesController as AdminListEmployeesController;
use App\Http\Controllers\Admin\Users\UpdateEmployeeController as AdminUpdateEmployeeController;
use App\Http\Controllers\Admin\Cinema\ToggleCinemaStatusController as AdminToggleCinemaStatusController;

/*
|--------------------------------------------------------------------------
| Routes d'administration
|--------------------------------------------------------------------------
*/

// Admin Cinema Routes (authentication required - admin only)
Route::middleware(['auth', 'role:administrateur'])->prefix('admin')
    ->name('admin.')
    ->group(function () {

        // Dashboard administrateur
        Route::get('/dashboard', AdminDashboardController::class)->name('dashboard');

        Route::prefix('cinemas')->name('cinemas.')
            ->group(function () {
                Route::get('/', AdminListCinemasController::class)->name('index');
                Route::get('/create', AdminCreateCinemaController::class)->name('create');
                Route::post('/', AdminStoreCinemaController::class)->name('store');
                Route::get('/{uuid}', AdminShowCinemaController::class)->name('show');
                Route::get('/{uuid}/edit', AdminEditCinemaController::class)->name('edit');
                Route::put('/{uuid}', AdminUpdateCinemaController::class)->name('update');
                Route::post('/{uuid}/toggle-status', AdminToggleCinemaStatusController::class)->name('toggle-status');
            });

        // Admin Film Routes
        Route::prefix('films')->name('films.')
            ->group(function () {
                Route::get('/', AdminListFilmsController::class)->name('index');
                Route::get('/create', AdminCreateFilmController::class)->name('create');
                Route::post('/', AdminStoreFilmController::class)->name('store');
                Route::get('/{uuid}', AdminShowFilmController::class)->name('show');
                Route::get('/{uuid}/edit', AdminEditFilmController::class)->name('edit');
                Route::put('/{uuid}', AdminUpdateFilmController::class)->name('update');
            });

        // Admin Salle Routes
        Route::prefix('salles')->name('salles.')
            ->group(function () {
                Route::get('/', \App\Http\Controllers\Admin\Salle\ListSallesController::class)->name('index');
                Route::get('/create', \App\Http\Controllers\Admin\Salle\CreateSalleController::class)->name('create');
                Route::post('/', \App\Http\Controllers\Admin\Salle\StoreSalleController::class)->name('store');
                Route::get('/{uuid}', \App\Http\Controllers\Admin\Salle\ShowSalleController::class)->name('show');
                Route::get('/{uuid}/edit', \App\Http\Controllers\Admin\Salle\EditSalleController::class)->name('edit');
                Route::put('/{uuid}', \App\Http\Controllers\Admin\Salle\UpdateSalleController::class)->name('update');
            });

        // Admin Seance Routes
        Route::prefix('seances')->name('seances.')
            ->group(function () {
                Route::get('/', \App\Http\Controllers\Admin\Seance\ListSeancesController::class)->name('index');
                Route::get('/create', \App\Http\Controllers\Admin\Seance\CreateSeanceController::class)->name('create');
                Route::post('/', \App\Http\Controllers\Admin\Seance\StoreSeanceController::class)->name('store');
                Route::get('/{uuid}', \App\Http\Controllers\Admin\Seance\ShowSeanceController::class)->name('show');
                Route::get('/{uuid}/edit', \App\Http\Controllers\Admin\Seance\EditSeanceController::class)->name('edit');
                Route::put('/{uuid}', \App\Http\Controllers\Admin\Seance\UpdateSeanceController::class)->name('update');

                // Route::delete('/{uuid}', \App\Http\Controllers\Admin\Seance\DeleteSeanceController::class)->name('destroy');
            });

        // Admin Reservation Routes
        Route::prefix('reservations')->name('reservations.')
            ->group(function () {
                Route::get('/', ListReservationsController::class)->name('index');
                Route::get('/{uuid}', ShowReservationController::class)->name('show');
            });

        // Admin User Routes
        Route::prefix('users')->name('users.')
            ->group(function () {
                // Client Routes
                Route::prefix('clients')->name('clients.')
                    ->group(function () {
                        Route::get('/', AdminListClientsController::class)->name('index');
                        Route::get('/{uuid}', AdminShowClientController::class)->name('show');
                        Route::get('/{uuid}/edit', AdminEditClientController::class)->name('edit');
                        Route::put('/{uuid}', AdminUpdateClientController::class)->name('update');
                    });

                // Employee Routes
                Route::prefix('employees')->name('employees.')
                    ->group(function () {
                        Route::get('/', AdminListEmployeesController::class)->name('index');
                        Route::get('/{uuid}', AdminShowEmployeeController::class)->name('show');
                        Route::get('/{uuid}/edit', AdminEditEmployeeController::class)->name('edit');
                        Route::put('/{uuid}', AdminUpdateEmployeeController::class)->name('update');

                        // Employee Job Routes
                        Route::get('/{uuid}/emploi/edit', [\App\Http\Controllers\Admin\Users\EmployeeEmploiController::class, 'edit'])->name('emploi.edit');
                        Route::put('/{uuid}/emploi', [\App\Http\Controllers\Admin\Users\EmployeeEmploiController::class, 'update'])->name('emploi.update');
                    });
            });
    });

// Employee Dashboard Routes (gestion quotidienne du cinéma) - DISABLED - moved to /routes/web/employee.php
/*
Route::middleware(['auth', 'role:employe,administrateur'])->prefix('gestion')
    ->name('employee.')
    ->group(function () {
        // Dashboard principal
        Route::get('/', [EmployeeDashboardController::class, 'index'])->name('dashboard');

        // Séances
        Route::prefix('seances')->name('seances.')
            ->group(function () {
            Route::get('/aujourd-hui', [EmployeeDashboardController::class, 'seancesToday'])->name('today');
            Route::get('/semaine', [EmployeeDashboardController::class, 'seancesWeek'])->name('week');
        });

        // Réservations
        Route::prefix('reservations')->name('reservations.')
            ->group(function () {
            Route::get('/aujourd-hui', [EmployeeDashboardController::class, 'reservationsToday'])->name('today');
        });

        // Incidents
        Route::prefix('incidents')->name('incidents.')
            ->group(function () {
            Route::get('/', [EmployeeDashboardController::class, 'incidentsList'])->name('index');
            Route::get('/declarer', [EmployeeDashboardController::class, 'incidentForm'])->name('create');
            Route::post('/declarer', [EmployeeDashboardController::class, 'incidentStore'])->name('store');
        });
    });
*/
