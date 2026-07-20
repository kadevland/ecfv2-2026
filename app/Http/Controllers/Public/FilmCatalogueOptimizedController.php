<?php

declare(strict_types=1);

namespace App\Http\Controllers\Public;

use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Infrastructure\Database\ReadModels\FilmCatalogue;

/**
 * Contrôleur optimisé pour le catalogue films MongoDB
 * Utilise les nouvelles fonctionnalités d'agrégation MongoDB
 */
class FilmCatalogueOptimizedController extends Controller
{
    /**
     * Affiche le catalogue de films avec pagination optimisée
     */
    public function index(Request $request): View
    {
        $query = FilmCatalogue::enDiffusion();

        // Filtres optimisés
        if ($request->filled('genre')) {
            $query->byGenre($request->genre);
        }

        if ($request->filled('classification')) {
            $query->byClassification($request->classification);
        }

        if ($request->filled('note_min')) {
            $query->minNote((float) $request->note_min);
        }

        if ($request->filled('duree_min') && $request->filled('duree_max')) {
            $query->durationBetween(
                (int) $request->duree_min,
                (int) $request->duree_max
            );
        }

        if ($request->filled('cinema_id')) {
            $query->byCinema($request->cinema_id);
        }

        if ($request->filled('ville')) {
            $query->inCity($request->ville);
        }

        if ($request->filled('search')) {
            $query->searchAdvanced($request->search);
        }

        // Tri optimisé
        $sortBy = $request->get('sort', 'recent');
        match ($sortBy) {
            'popular' => $query->popular(),
            'note'    => $query->orderBy('note_moyenne', 'desc'),
            'title'   => $query->orderBy('titre', 'asc'),
            default   => $query->recent()
        };

        $films = $query->paginate(20);

        // Statistiques du catalogue via agrégation
        $stats = [
            'total_films'  => FilmCatalogue::enDiffusion()->count(),
            'genres_stats' => FilmCatalogue::getGenreStats()->toArray(),
        ];

        /** @phpstan-ignore-next-line argument.type */
        return view('public.films.catalogue-optimized', compact('films', 'stats'));
    }

    /**
     * Recherche avancée avec score de pertinence
     */
    public function searchAdvanced(Request $request): JsonResponse
    {
        $request->validate([
            'q'     => 'required|string|min:2|max:100',
            'limit' => 'integer|min:1|max:50',
        ]);

        $results = FilmCatalogue::searchWithRelevance(
            $request->q,
            $request->get('limit', 20)
        );

        return response()->json([
            'query'   => $request->q,
            'results' => $results->toArray(),
            'count'   => count($results),
        ]);
    }

    /**
     * Obtient les films populaires par cinéma
     */
    public function popularByCinema(Request $request, string $cinemaId): JsonResponse
    {
        $request->validate([
            'limit' => 'integer|min:1|max:20',
        ]);

        $films = FilmCatalogue::getPopularFilmsByCinema(
            $cinemaId,
            $request->get('limit', 10)
        );

        return response()->json([
            'cinema_id' => $cinemaId,
            'films'     => $films->toArray(),
        ]);
    }

    /**
     * Obtient les statistiques par genre
     */
    public function genreStats(): JsonResponse
    {
        $stats = FilmCatalogue::getGenreStats();

        return response()->json([
            'genres' => $stats->toArray(),
        ]);
    }

    /**
     * Planning des séances par jour
     */
    public function dailySchedule(Request $request): JsonResponse
    {
        $request->validate([
            'date_start' => 'required|date',
            'date_end'   => 'required|date|after_or_equal:date_start',
        ]);

        $schedule = FilmCatalogue::getFilmsWithDailySeances(
            $request->date_start,
            $request->date_end
        );

        return response()->json([
            'period' => [
                'start' => $request->date_start,
                'end'   => $request->date_end,
            ],
            'schedule' => $schedule->toArray(),
        ]);
    }

    /**
     * Recommandations personnalisées basées sur les préférences
     */
    public function recommendations(Request $request): JsonResponse
    {
        $request->validate([
            'genres'    => 'array',
            'genres.*'  => 'string',
            'note_min'  => 'numeric|min:0|max:5',
            'duree_max' => 'integer|min:60|max:300',
            'limit'     => 'integer|min:1|max:20',
        ]);

        $query = FilmCatalogue::enDiffusion()
            ->withAvailableSeances();

        // Filtrage basé sur les préférences
        if ($request->filled('genres')) {
            $query->whereIn('genre', $request->genres);
        }

        if ($request->filled('note_min')) {
            $query->minNote($request->note_min);
        }

        if ($request->filled('duree_max')) {
            $query->maxDuration($request->duree_max);
        }

        // Tri par popularité et note
        $recommendations = $query
            ->popularWithMinRating($request->get('note_min', 3.0))
            ->limit($request->get('limit', 10))
            ->get();

        return response()->json([
            'preferences'     => $request->only(['genres', 'note_min', 'duree_max']),
            'recommendations' => $recommendations,
        ]);
    }

    /**
     * Filtre rapide pour l'auto-complétion
     */
    public function quickSearch(Request $request): JsonResponse
    {
        $request->validate([
            'q' => 'required|string|min:1|max:50',
        ]);

        $query = $request->q;

        // Recherche rapide dans titre uniquement pour l'auto-complétion
        $films = FilmCatalogue::enDiffusion()
            ->where('titre', 'regex', new \MongoDB\BSON\Regex($query, 'i'))
            ->limit(10)
            ->get(['film_id', 'titre', 'genre', 'note_moyenne']);

        // Recherche aussi dans les réalisateurs
        $directors = FilmCatalogue::enDiffusion()
            ->byDirector($query)
            ->limit(5)
            ->get(['film_id', 'titre', 'realisateur']);

        return response()->json([
            'query'     => $query,
            'films'     => $films,
            'directors' => $directors,
        ]);
    }

    /**
     * Détails d'un film avec informations enrichies
     */
    public function show(string $filmId): View
    {
        $film = FilmCatalogue::where('film_id', $filmId)
            ->enDiffusion()
            ->firstOrFail();

        // Films similaires basés sur le genre et la note
        $similars = FilmCatalogue::enDiffusion()
            ->byGenre($film->genre)
            ->where('film_id', '!=', $filmId)
            ->minNote($film->note_moyenne - 0.5)
            ->popular()
            ->limit(6)
            ->get();

        // Cinémas qui diffusent ce film
        $cinemas = collect($film->cinemas_diffusion)
            ->unique('cinema_id')
            ->values();

        /** @phpstan-ignore-next-line argument.type */
        return view('public.films.show-optimized', compact('film', 'similars', 'cinemas'));
    }

    /**
     * API pour obtenir les films par filtres multiples
     */
    public function apiFiltered(Request $request): JsonResponse
    {
        $request->validate([
            'genres'          => 'array',
            'classifications' => 'array',
            'note_min'        => 'numeric|min:0|max:5',
            'note_max'        => 'numeric|min:0|max:5',
            'duree_min'       => 'integer|min:60',
            'duree_max'       => 'integer|min:60',
            'cinema_ids'      => 'array',
            'ville'           => 'string|max:100',
            'search'          => 'string|max:100',
            'sort'            => 'in:recent,popular,note,title',
            'page'            => 'integer|min:1',
            'per_page'        => 'integer|min:1|max:50',
        ]);

        $query = FilmCatalogue::enDiffusion();

        // Application des filtres
        if ($request->filled('genres')) {
            $query->whereIn('genre', $request->genres);
        }

        if ($request->filled('classifications')) {
            $query->whereIn('classification', $request->classifications);
        }

        if ($request->filled('note_min') && $request->filled('note_max')) {
            $query->noteBetween($request->note_min, $request->note_max);
        } elseif ($request->filled('note_min')) {
            $query->minNote($request->note_min);
        }

        if ($request->filled('duree_min') && $request->filled('duree_max')) {
            $query->durationBetween($request->duree_min, $request->duree_max);
        }

        if ($request->filled('cinema_ids')) {
            $query->inCinemas($request->cinema_ids);
        }

        if ($request->filled('ville')) {
            $query->inCity($request->ville);
        }

        if ($request->filled('search')) {
            $query->searchAdvanced($request->search);
        }

        // Application du tri
        $sortBy = $request->get('sort', 'recent');
        match ($sortBy) {
            'popular' => $query->popular(),
            'note'    => $query->orderBy('note_moyenne', 'desc'),
            'title'   => $query->orderBy('titre', 'asc'),
            default   => $query->recent()
        };

        $films = $query->paginate($request->get('per_page', 20));

        return response()->json([
            'films'      => $films->items(),
            'pagination' => [
                'current_page' => $films->currentPage(),
                'total_pages'  => $films->lastPage(),
                'total_items'  => $films->total(),
                'per_page'     => $films->perPage(),
            ],
            'filters_applied' => $request->only([
                'genres', 'classifications', 'note_min', 'note_max',
                'duree_min', 'duree_max', 'cinema_ids', 'ville', 'search',
            ]),
        ]);
    }
}
