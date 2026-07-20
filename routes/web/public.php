<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\WebAuthController;
use App\Http\Controllers\Public\SeanceController;
use App\Http\Controllers\Public\AccueilController;
use App\Http\Controllers\User\UserProfileController;
use App\Http\Controllers\Public\ReservationController;
use App\Http\Controllers\Public\FilmCatalogueController;
use App\Http\Controllers\Public\Ticket\ShowQRCodeController;
use App\Http\Controllers\Public\Account\ShowAccountController;
use App\Http\Controllers\Public\Ticket\DownloadTicketController;
use App\Http\Controllers\Public\Reservation\FilmSeancesController;
use App\Http\Controllers\Public\Account\ShowReservationsController;
use App\Http\Controllers\Public\Reservation\ReservationIndexController;
use App\Http\Controllers\Public\ShowCinemaController as PublicShowCinemaController;
use App\Http\Controllers\Public\ListCinemasController as PublicListCinemasController;

/*
|--------------------------------------------------------------------------
| Routes publiques du site
|--------------------------------------------------------------------------
*/

// Route::get('/', [AccueilController::class, 'index'])->name('home'); // Déjà définie dans web.php

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [WebAuthController::class, 'showLoginForm'])->name('login');

    Route::middleware(['throttle:5,1'])->group(function () {
        Route::post('/login', [WebAuthController::class, 'login']);
        Route::get('/register', [WebAuthController::class, 'showRegistrationForm'])->name('register');
        // Route::get('/forgot-password', [WebAuthController::class, 'showForgotPasswordForm'])->name('forgot-password');
    });

});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [WebAuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [UserProfileController::class, 'dashboard'])->name('dashboard');
});

// Public Cinema Routes
Route::prefix('cinemas')->name('cinemas.')
    ->group(function () {
        Route::get('/', PublicListCinemasController::class)->name('index');
        Route::get('/{uuid}', PublicShowCinemaController::class)->name('show');
    });

// Public Films Routes
Route::prefix('films')->name('films.')
    ->group(function () {
        Route::get('/', [FilmCatalogueController::class, 'index'])->name('index');
        Route::get('/search', [FilmCatalogueController::class, 'index'])->name('search'); // Redirect search to index with query
        Route::get('/genre/{genre}', [FilmCatalogueController::class, 'byGenre'])->name('by-genre');
        Route::get('/{id}', [FilmCatalogueController::class, 'show'])->name('show');
        Route::get('/{id}/seances', [FilmCatalogueController::class, 'seances'])->name('seances');
        Route::get('/{id}/avis', [FilmCatalogueController::class, 'ratings'])->name('ratings');
        Route::post('/{id}/avis', [FilmCatalogueController::class, 'storeRating'])->name('ratings.store');
    });

// Public Search Route (HTMX)
Route::get('/search', \App\Http\Controllers\ModalSearchController::class)->name('search');

// Public Seances Routes
Route::prefix('seances')->name('seances.')
    ->group(function () {
        Route::get('/', [SeanceController::class, 'index'])->name('index');
    });

// Public Reservation Routes
Route::prefix('reservation')->name('reservation.')
    ->group(function () {
        Route::get('/', ReservationIndexController::class)->name('index');
        Route::get('/film/{filmId}/seances', FilmSeancesController::class)->name('film.seances');
    });

// Public Reservation Flow Routes (authentication required for booking)
Route::get('/seance/{seance_id}/reserver', [ReservationController::class, 'showSeatSelection'])->name('seance.reserver');
Route::middleware(['auth'])->group(function () {
    Route::post('/reservation', [ReservationController::class, 'createReservation'])->name('reservation.create');
    Route::get('/confirmation', [ReservationController::class, 'confirmation'])->name('reservation.confirmation');
    Route::post('/reservation/payment', \App\Http\Controllers\Public\Reservation\ProcessPaymentController::class)->name('reservation.payment.process');
});

// Public Account Route (authentication required)
Route::middleware(['auth'])->group(function () {
    Route::get('/mon-compte', ShowAccountController::class)->name('account');
    Route::get('/mon-compte/reservations', ShowReservationsController::class)->name('account.reservations');

    // Route de test pour debugger le problème de performance
    // Route::get('/test-reservations', \App\Http\Controllers\Public\Account\TestReservationsController::class)->name('test.reservations');
});

// Public QR Code Route
Route::get('/qr/{reservationNumber}', ShowQRCodeController::class)->name('qr.show');

// Public PDF Ticket Route
Route::get('/billet/{reservationNumber}', DownloadTicketController::class)->name('ticket.download');
