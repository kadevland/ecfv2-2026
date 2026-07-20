<?php

declare(strict_types=1);

namespace App\Http\Controllers\Employee;

use Illuminate\View\View;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Domain\Enums\ClassificationFilm;
use App\Infrastructure\Database\Models\Cinema\Seance;

final class FilmController extends Controller
{
    public function index(Request $request): View
    {
        // Films programmés aujourd'hui avec leurs séances
        $query = Seance::with(['film', 'salle'])
            ->whereDate('date_heure_debut', now()->toDateString())
            ->orderBy('date_heure_debut');

        // Filtre par classification si demandé
        if ($request->filled('classification')) {
            $query->whereHas('film', function ($q) use ($request) {
                $q->where('classification', $request->input('classification'));
            });
        }

        $seances = $query->get();

        // Grouper par film
        $filmsAvecSeances = $seances->groupBy('film_uuid')->map(function ($seancesFilm) {
            $film = $seancesFilm->first()->film;

            return [
                'film'            => $film,
                'seances'         => $seancesFilm,
                'nb_seances'      => $seancesFilm->count(),
                'premiere_seance' => $seancesFilm->first()->date_heure_debut,
                'derniere_seance' => $seancesFilm->last()->date_heure_debut,
            ];
        })->sortBy('premiere_seance');

        // Classifications disponibles pour le filtre
        $classificationsDisponibles = [];
        foreach (ClassificationFilm::cases() as $classification) {
            $classificationsDisponibles[$classification->value] = $classification->label();
        }

        return view('employee.films.index', [
            'filmsAvecSeances'           => $filmsAvecSeances,
            'classificationsDisponibles' => $classificationsDisponibles,
            'filters'                    => $request->all(['classification']),
            'dateJour'                   => now()->format('d/m/Y'),
        ]);
    }
}
