<?php

declare(strict_types=1);

namespace App\Http\Controllers\Public\Accueil;

use Exception;
use Carbon\Carbon;
use Illuminate\View\View;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\MongoDB\FilmCatalogueQueryService;
use App\Services\MongoDB\SeanceQueryService;
use App\Infrastructure\Database\ReadModels\FilmCatalogue;

/**
 * Contrôleur pour afficher la page d'accueil
 * RESPONSABILITÉ UNIQUE : Afficher la page d'accueil
 */
class ShowAccueilController extends Controller
{
    public function __construct (
        /** @phpstan-ignore-next-line property.onlyWritten */
        private FilmCatalogueQueryService $filmService,
        /** @phpstan-ignore-next-line property.onlyWritten */
        private SeanceQueryService $seanceService
    ) {}

    /**
     * Affiche la page d'accueil
     * GET /
     */
    public function __invoke (Request $request) : View
    {
        try {
            // Récupérer les statistiques réelles
            $homeStats = $this->seanceService->getHomeStats();
            $filmStats = $this->seanceService->getFilmStats();

            $data = [
                'currentWeekFilms' => $this->getFilmsCurrentWeek(8),
                'upcomingFilms'    => $this->getFilmsUpcoming(8),
                'topRatedFilms'    => $this->getTopRatedFilms(6),
                'stats'            => [
                    'total_films'  => $filmStats['total_films'],
                    'genres_count' => $filmStats['genres_count'],
                    'avg_rating'   => $filmStats['avg_rating'],
                ],
                'totalFilms'       => $filmStats['total_films'],
                'totalCinemas'     => $homeStats['total_cinemas'],
                'homeStats'        => $homeStats,
            ];
        } catch (Exception $e) {
            // Fallback avec collections vides
            $data = [
                'currentWeekFilms' => collect(),
                'upcomingFilms'    => collect(),
                'topRatedFilms'    => collect(),
                'stats'            => [
                    'total_films'  => 0,
                    'genres_count' => 0,
                    'avg_rating'   => 0,
                ],
                'totalFilms'       => 0,
                'totalCinemas'     => 0,
                'homeStats'        => [],
            ];
        }

        return view('welcome', $data);
    }

    /**
     * Récupère les données pour la page d'accueil
     *
     * @return array<string, mixed>
     *
     * @phpstan-ignore-next-line method.unused
     */
    private function getHomePageData () : array
    {
        // Temporaire : Utiliser des collections vides pour éviter les erreurs
        $featuredFilms = collect();
        $topRatedFilms = collect();

        try {
            $featuredFilms = FilmCatalogue::actif()->limit(8)
                ->get();
            $topRatedFilms = FilmCatalogue::actif()->limit(6)
                ->get();
        } catch (Exception $e) {
            // Ignore errors pour l'instant
        }

        return [
            'featuredFilms' => $featuredFilms,
            'topRatedFilms' => $topRatedFilms,
            'stats'         => [
                'total_films'  => $featuredFilms->count(),
                'genres_count' => 12, // Hardcoded pour l'instant
                'avg_rating'   => 4.2, // Hardcoded pour l'instant
            ],
            'totalFilms'    => $featuredFilms->count(),
            'totalCinemas'  => 7,
        ];
    }

    /**
     * Récupère les films de la semaine courante (mercredi à mardi)
     * Basé sur les séances réellement programmées dans seance_publics
     */
    private function getFilmsCurrentWeek (int $limit)
    {
        try {
            return $this->seanceService->getFilmsCurrentWeek($limit);
        } catch (Exception $e) {
            logger()->error('Erreur récupération films de la semaine', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return collect();
        }
    }

    /**
     * Récupère les films à venir (2 prochaines semaines)
     * Basé sur les séances futures programmées dans seance_publics
     */
    private function getFilmsUpcoming (int $limit)
    {
        try {
            return $this->seanceService->getFilmsUpcoming($limit);
        } catch (Exception $e) {
            logger()->error('Erreur récupération films à venir', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return collect();
        }
    }

    /**
     * Récupère les films les mieux notés
     */
    private function getTopRatedFilms (int $limit)
    {
        try {
            return FilmCatalogue::where('statut_diffusion', 'en_diffusion')
                ->where('note_moyenne', '>=', 4.0)
                ->orderBy('note_moyenne', 'desc')
                ->limit($limit)
                ->get();
        } catch (Exception $e) {
            return collect();
        }
    }
}
