<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use Illuminate\View\View;
use App\Infrastructure\Database\Models\Reservations\Reservation;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Infrastructure\Database\Models\Cinema\Cinema;
use App\Infrastructure\Database\Models\Cinema\Film;
use App\Infrastructure\Database\Models\Cinema\Seance;

/**
 * Dashboard administrateur - infos génériques
 */
class AdminDashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        // Stats génériques light - données de test pour le moment
        $stats = [
            'total_seances'      => Seance::query()->wherePlaying()->count(),
            'total_reservations' => Reservation::query()->whereForPlayingSceance()->count(),
            'total_cinemas'      => Cinema::query()->active()->count(),
            'total_films'        => Film::query()->whereInTheaters()->count(),
        ];

        return view('admin.dashboard', [
            'stats' => $stats,
        ]);
    }
}
