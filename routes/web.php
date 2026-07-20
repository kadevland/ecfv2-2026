<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HealthController;
use App\Http\Controllers\ModalSearchController;
use App\Http\Controllers\Public\Accueil\ClearCacheController;
use App\Http\Controllers\Public\Accueil\ShowAccueilController;
use App\Http\Controllers\Public\Accueil\AccueilStatsController;
use App\Http\Controllers\Public\Accueil\FilmsCarouselController;
use App\Http\Controllers\Public\Accueil\AccueilSuggestionsController;

Route::get('/', ShowAccueilController::class)->name('home');

// Health Check routes (pour load balancers et monitoring)
Route::get('/health', [HealthController::class, 'simple'])->name('health.simple');
Route::get('/health/full', [HealthController::class, 'index'])->name('health.full');
Route::get('/health/system', [HealthController::class, 'system'])->name('health.system');

// Modal search route
Route::get('/modal-search', ModalSearchController::class)->name('modal.search');

// // API routes pour la homepage
Route::prefix('api/home')->name('api.home.')
    ->group(function () {
        Route::get('/films-carousel', FilmsCarouselController::class)->name('films-carousel');
        Route::get('/stats', AccueilStatsController::class)->name('stats');
        Route::get('/suggestions', AccueilSuggestionsController::class)->name('suggestions');
        Route::post('/clear-cache', ClearCacheController::class)->name('clear-cache');
    });

require_once __DIR__ . '/auth.php';

require_once __DIR__ . '/web/public.php';

require __DIR__ . '/web/employee.php';

require __DIR__ . '/web/admin.php';
//Route::get('/mockup-home', fn () => view('mockup-home'));
