<?php

declare(strict_types=1);

namespace App\Http\Controllers\Employee;

use Illuminate\View\View;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Infrastructure\Database\Models\Cinema\Seance;

final class SeanceController extends Controller
{
    public function index(Request $request): View
    {
        // Récupérer les séances du jour directement avec Eloquent
        $query = Seance::with([
            'film',
            'salle',
            'reservations',
        ])
            ->whereDate('date_heure_debut', now()->toDateString());

        // Appliquer les filtres
        if ($request->filled('salle')) {
            $query->whereHas('salle', function ($q) use ($request) {
                $q->where('nom', $request->input('salle'));
            });
        }

        if ($request->filled('film')) {
            $query->whereHas('film', function ($q) use ($request) {
                $q->where('titre', $request->input('film'));
            });
        }

        if ($request->filled('statut')) {
            $now = now();
            switch ($request->input('statut')) {
                case 'a_venir':
                    $query->where('date_heure_debut', '>', $now);
                    break;
                case 'en_cours':
                    $query->where('date_heure_debut', '<=', $now)
                        ->where('date_heure_fin', '>', $now);
                    break;
                case 'termine':
                    $query->where('date_heure_fin', '<=', $now);
                    break;
            }
        }

        $seances = $query->orderBy('date_heure_debut', 'asc')->paginate(20);

        // Récupérer toutes les salles et films disponibles pour les filtres
        $sallesDisponibles = \App\Infrastructure\Database\Models\Cinema\Salle::select('nom')
            ->whereHas('seances', function ($q) {
                $q->whereDate('date_heure_debut', now()->toDateString());
            })
            ->orderBy('nom')
            ->pluck('nom')
            ->unique()
            ->toArray();

        $filmsDisponibles = \App\Infrastructure\Database\Models\Cinema\Film::select('titre')
            ->whereHas('seances', function ($q) {
                $q->whereDate('date_heure_debut', now()->toDateString());
            })
            ->orderBy('titre')
            ->pluck('titre')
            ->unique()
            ->toArray();

        return view('employee.seances.index', [
            'seances'           => $seances,
            'sallesDisponibles' => $sallesDisponibles,
            'filmsDisponibles'  => $filmsDisponibles,
            'filters'           => $request->all(['salle', 'film', 'statut']),
            'dateJour'          => now()->format('d/m/Y'),
        ]);
    }
}
