<?php

declare(strict_types=1);

namespace App\Http\Controllers\Public;

use Carbon\Carbon;
use Illuminate\View\View;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Domain\Public\Repositories\SeanceRepositoryInterface;

class SeanceController extends Controller
{
    public function __construct(
        protected SeanceRepositoryInterface $seanceRepository
    ) {}

    /**
     * Afficher toutes les séances disponibles
     */
    public function index(Request $request): View
    {
        // Récupérer les filtres
        $filters = [
            'date'   => $request->get('date'),
            'cinema' => $request->get('cinema'),
            'genre'  => $request->get('genre'),
        ];

        // Récupérer les séances via repository
        $seancesData = $this->seanceRepository->getSeancesForIndex($filters);

        // Récupérer les IDs de films uniques
        $filmIds = $seancesData->pluck('film_id')->unique();

        // Récupérer les infos de films via repository
        $films = $this->seanceRepository->getFilmsByIds($filmIds->toArray());

        // Transformer et grouper les séances
        $seancesTransformed = $seancesData->map(function ($seance) use ($films) {
            $film = $films->get($seance->film_id);

            return (object) [
                'seance_id'    => $seance->seance_id,
                'film_id'      => $seance->film_id,
                'film_titre'   => $film->titre ?? 'Film inconnu',
                'film_affiche' => $film->affiche_url ?? null,
                'film_duree'   => $film->duree_minutes ?? null,
                'film_genre'   => is_array($film->genres ?? []) && !empty($film->genres)
                                        ? $film->genres[0] : null,
                'film_classification' => $film->classification ?? null,
                'cinema_id'           => (string) $seance->cinema_id,
                'cinema_nom'          => $seance->cinema_nom,
                'salle_nom'           => $seance->salle_nom,
                'date_heure_debut'    => Carbon::parse($seance->date_heure_debut),
                'date_heure_fin'      => Carbon::parse($seance->date_heure_fin),
                'places_disponibles'  => $seance->places_disponibles,
                'version'             => $seance->version ?? 'VF',
                'qualite_projection'  => $seance->qualite_projection ?? 'Standard',
                'tarif_base'          => 12.50, // À récupérer depuis la config ou la séance
            ];
        });

        // Grouper par date puis par film
        $seancesByDate = $seancesTransformed
            ->groupBy(function ($seance) {
                return $seance->date_heure_debut->format('Y-m-d');
            })
            ->map(function ($seancesOfDay) {
                return $seancesOfDay->groupBy('film_id')
                    ->map(function ($seancesOfFilm) {
                        return (object) [
                            'film' => (object) [
                                'id'             => $seancesOfFilm->first()->film_id,
                                'titre'          => $seancesOfFilm->first()->film_titre,
                                'affiche'        => $seancesOfFilm->first()->film_affiche,
                                'duree'          => $seancesOfFilm->first()->film_duree,
                                'genre'          => $seancesOfFilm->first()->film_genre,
                                'classification' => $seancesOfFilm->first()->film_classification,
                            ],
                            'seances' => $seancesOfFilm->groupBy('cinema_id'),
                        ];
                    });
            });

        // Récupérer les cinémas disponibles pour le filtre
        $cinemasDisponibles = $this->seanceRepository->getAvailableCinemas();

        // Récupérer les genres disponibles
        $genresDisponibles = $this->seanceRepository->getUniqueGenres();

        // Pagination manuelle
        $perPage     = 10; // 10 dates par page
        $currentPage = $request->get('page', 1);
        $totalDates  = $seancesByDate->count();

        $seancesPaginated = $seancesByDate->slice(($currentPage - 1) * $perPage, $perPage);

        $pagination = new \Illuminate\Pagination\LengthAwarePaginator(
            $seancesPaginated,
            $totalDates,
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('public.seances.index', [
            'seances'            => $seancesPaginated,
            'pagination'         => $pagination,
            'filters'            => $filters,
            'cinemasDisponibles' => $cinemasDisponibles,
            'genresDisponibles'  => $genresDisponibles,
            'totalSeances'       => $seancesTransformed->count(),
        ]);
    }
}
