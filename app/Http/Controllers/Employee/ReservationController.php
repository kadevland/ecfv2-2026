<?php

declare(strict_types=1);

namespace App\Http\Controllers\Employee;

use Illuminate\View\View;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Infrastructure\Database\Models\Reservations\Reservation;

final class ReservationController extends Controller
{
    public function index(Request $request): View
    {
        // Récupérer les réservations du jour directement avec Eloquent
        $query = Reservation::with([
            'user.credential',
            'user.profil',
            'seance.film',
            'seance.salle',
        ])
            ->whereDate('created_at', now()->toDateString());

        // Appliquer les filtres
        if ($request->filled('film')) {
            $query->whereHas('seance.film', function ($q) use ($request) {
                $q->where('titre', $request->input('film'));
            });
        }

        if ($request->filled('code')) {
            $query->where('numero_reservation', 'LIKE', '%' . $request->input('code') . '%');
        }

        $reservations = $query->orderBy('created_at', 'desc')->paginate(20);

        // Récupérer tous les films disponibles pour les filtres
        $filmsDisponibles = \App\Infrastructure\Database\Models\Cinema\Film::select('titre')
            ->whereHas('seances.reservations', function ($q) {
                $q->whereDate('created_at', now()->toDateString());
            })
            ->orderBy('titre')
            ->pluck('titre')
            ->unique()
            ->toArray();

        return view('employee.reservations.index', [
            'reservations'     => $reservations,
            'filmsDisponibles' => $filmsDisponibles,
            'filters'          => $request->all(['film', 'code']),
            'dateJour'         => now()->format('d/m/Y'),
        ]);
    }
}
