<?php

declare(strict_types=1);

namespace App\Services\MongoDB;

use Carbon\Carbon;
use MongoDB\BSON\UTCDateTime;
use Illuminate\Support\Collection;
use App\Infrastructure\Database\ReadModels\SeancePublic;
use App\Infrastructure\Database\ReadModels\FilmCatalogue;
use App\Infrastructure\Database\Schemas\Cinema\SeancePublicSchema;
use App\Infrastructure\Database\Schemas\Cinema\FilmCatalogueSchema;

/**
 * Service pour optimiser les requêtes MongoDB basées sur les séances
 * Centralise la logique métier pour déterminer les films programmés
 */
class SeanceQueryService
{
    public function __construct(
        private SeancePublic $seanceModel,
        private FilmCatalogue $filmModel
    ) {}

    /**
     * Récupère les films de la semaine courante (mercredi à mardi)
     * Basés sur les séances réellement programmées
     */
    public function getFilmsCurrentWeek(int $limit = 8): Collection
    {
        // Calculer la période de la semaine cinéma
        $now = now();
        $weekStart = $now->copy()->startOfWeek(Carbon::WEDNESDAY);
        
        if ($now->dayOfWeek < Carbon::WEDNESDAY) {
            $weekStart->subWeek();
        }
        
        $weekEnd = $weekStart->copy()->addDays(6)->endOfDay();

        // ÉTAPE 1: Récupérer les séances de la période
        $seances = $this->seanceModel
            ->whereBetween(SeancePublicSchema::DATE_HEURE_DEBUT, [$weekStart, $weekEnd])
            ->where(SeancePublicSchema::STATUT, 'PROGRAMMEE')
            ->get([SeancePublicSchema::FILM_ID, SeancePublicSchema::FILM_TITRE, SeancePublicSchema::DATE_HEURE_DEBUT, SeancePublicSchema::CINEMA_ID]);

        if ($seances->isEmpty()) {
            return collect();
        }

        // ÉTAPE 2: Grouper par film et compter les séances
        $filmsWithSeances = $seances->groupBy(SeancePublicSchema::FILM_ID)->map(function ($filmSeances) {
            return [
                'film_id' => $filmSeances->first()->film_id,
                'titre' => $filmSeances->first()->film_titre,
                'seances_count' => $filmSeances->count(),
                'cinemas_count' => $filmSeances->pluck('cinema_id')->unique()->count(),
                'premiere_seance' => $filmSeances->min(SeancePublicSchema::DATE_HEURE_DEBUT),
                'derniere_seance' => $filmSeances->max(SeancePublicSchema::DATE_HEURE_DEBUT),
            ];
        })->sortByDesc('seances_count')->take($limit);

        // ÉTAPE 3: Récupérer les détails complets des films
        $filmIds = $filmsWithSeances->keys()->toArray();
        $films = $this->filmModel
            ->whereIn(FilmCatalogueSchema::FILM_ID, $filmIds)
            ->where(FilmCatalogueSchema::STATUT_DIFFUSION, 'en_diffusion')
            ->get();

        // ÉTAPE 4: Enrichir avec les infos de séances
        return $films->map(function ($film) use ($filmsWithSeances) {
            $seanceData = $filmsWithSeances->get($film->film_id);
            if ($seanceData) {
                $film->seances_count = $seanceData['seances_count'];
                $film->cinemas_count = $seanceData['cinemas_count'];
                $film->premiere_seance = $seanceData['premiere_seance'];
                $film->derniere_seance = $seanceData['derniere_seance'];
            }
            return $film;
        })->sortByDesc('seances_count')->values();
    }

    /**
     * Récupère les films à venir basés sur les séances futures
     */
    public function getFilmsUpcoming(int $limit = 8): Collection
    {
        $now = now();
        $twoWeeksLater = $now->copy()->addWeeks(2);

        // ÉTAPE 1: Récupérer les séances futures
        $seances = $this->seanceModel
            ->whereBetween(SeancePublicSchema::DATE_HEURE_DEBUT, [$now, $twoWeeksLater])
            ->where(SeancePublicSchema::STATUT, 'PROGRAMMEE')
            ->get([SeancePublicSchema::FILM_ID, SeancePublicSchema::FILM_TITRE, SeancePublicSchema::DATE_HEURE_DEBUT, SeancePublicSchema::CINEMA_ID]);

        if ($seances->isEmpty()) {
            return collect();
        }

        // ÉTAPE 2: Grouper par film
        $filmsWithSeances = $seances->groupBy(SeancePublicSchema::FILM_ID)->map(function ($filmSeances) {
            return [
                'film_id' => $filmSeances->first()->film_id,
                'titre' => $filmSeances->first()->film_titre,
                'seances_count' => $filmSeances->count(),
                'cinemas_count' => $filmSeances->pluck('cinema_id')->unique()->count(),
                'premiere_seance' => $filmSeances->min(SeancePublicSchema::DATE_HEURE_DEBUT),
                'derniere_seance' => $filmSeances->max(SeancePublicSchema::DATE_HEURE_DEBUT),
            ];
        })->sortBy('premiere_seance')->take($limit);

        // ÉTAPE 3: Récupérer les détails des films
        $filmIds = $filmsWithSeances->keys()->toArray();
        $films = $this->filmModel
            ->whereIn(FilmCatalogueSchema::FILM_ID, $filmIds)
            ->where(FilmCatalogueSchema::STATUT_DIFFUSION, 'en_diffusion')
            ->get();

        // ÉTAPE 4: Enrichir et trier
        return $films->map(function ($film) use ($filmsWithSeances) {
            $seanceData = $filmsWithSeances->get($film->film_id);
            if ($seanceData) {
                $film->seances_count = $seanceData['seances_count'];
                $film->cinemas_count = $seanceData['cinemas_count'];
                $film->premiere_seance = $seanceData['premiere_seance'];
                $film->derniere_seance = $seanceData['derniere_seance'];
            }
            return $film;
        })->sortBy(function ($film) {
            return $film->premiere_seance ?? strtotime('+100 years');
        })->values();
    }

    /**
     * Récupère les films les plus programmés sur une période
     */
    public function getMostProgrammedFilms(int $days = 7, int $limit = 10): Collection
    {
        $startDate = now()->subDays($days)->startOfDay();
        $endDate = now()->endOfDay();

        // ÉTAPE 1: Récupérer les séances de la période
        $seances = $this->seanceModel
            ->whereBetween(SeancePublicSchema::DATE_HEURE_DEBUT, [$startDate, $endDate])
            ->where(SeancePublicSchema::STATUT, 'PROGRAMMEE')
            ->get([
                SeancePublicSchema::FILM_ID, 
                SeancePublicSchema::FILM_TITRE, 
                SeancePublicSchema::CINEMA_ID,
                SeancePublicSchema::PLACES_TOTALES,
                SeancePublicSchema::PLACES_DISPONIBLES
            ]);

        if ($seances->isEmpty()) {
            return collect();
        }

        // ÉTAPE 2: Calculer les stats par film
        $filmsWithStats = $seances->groupBy(SeancePublicSchema::FILM_ID)->map(function ($filmSeances) {
            $totalPlaces = $filmSeances->sum(SeancePublicSchema::PLACES_TOTALES);
            $availablePlaces = $filmSeances->sum(SeancePublicSchema::PLACES_DISPONIBLES);
            $soldPlaces = $totalPlaces - $availablePlaces;
            
            return [
                'film_id' => $filmSeances->first()->film_id,
                'titre' => $filmSeances->first()->film_titre,
                'seances_count' => $filmSeances->count(),
                'cinemas_count' => $filmSeances->pluck(SeancePublicSchema::CINEMA_ID)->unique()->count(),
                'total_places' => $totalPlaces,
                'available_places' => $availablePlaces,
                'sold_places' => $soldPlaces,
                'popularity_score' => $filmSeances->count() + ($filmSeances->pluck(SeancePublicSchema::CINEMA_ID)->unique()->count() * 2) + ($soldPlaces / 100),
            ];
        })->sortByDesc('popularity_score')->take($limit);

        // ÉTAPE 3: Récupérer les films
        $filmIds = $filmsWithStats->keys()->toArray();
        $films = $this->filmModel
            ->whereIn(FilmCatalogueSchema::FILM_ID, $filmIds)
            ->where(FilmCatalogueSchema::STATUT_DIFFUSION, 'en_diffusion')
            ->get();

        // ÉTAPE 4: Enrichir
        return $films->map(function ($film) use ($filmsWithStats) {
            $stats = $filmsWithStats->get($film->film_id);
            if ($stats) {
                $film->seances_count = $stats['seances_count'];
                $film->cinemas_count = $stats['cinemas_count'];
                $film->total_places = $stats['total_places'];
                $film->available_places = $stats['available_places'];
                $film->sold_places = $stats['sold_places'];
                $film->popularity_score = $stats['popularity_score'];
            }
            return $film;
        })->sortByDesc('popularity_score')->values();
    }

    /**
     * Récupère les films disponibles pour une date spécifique
     */
    public function getFilmsForDate(string $date, int $limit = 20): Collection
    {
        $targetDate = Carbon::parse($date)->startOfDay();
        $nextDay = $targetDate->copy()->endOfDay();

        // ÉTAPE 1: Récupérer les séances de cette date
        $seances = $this->seanceModel
            ->whereBetween(SeancePublicSchema::DATE_HEURE_DEBUT, [$targetDate, $nextDay])
            ->where(SeancePublicSchema::STATUT, 'PROGRAMMEE')
            ->get([
                SeancePublicSchema::FILM_ID, 
                SeancePublicSchema::FILM_TITRE, 
                SeancePublicSchema::CINEMA_ID,
                SeancePublicSchema::CINEMA_NOM,
                SeancePublicSchema::PLACES_DISPONIBLES
            ]);

        if ($seances->isEmpty()) {
            return collect();
        }

        // ÉTAPE 2: Grouper par film
        $filmsWithSeances = $seances->groupBy(SeancePublicSchema::FILM_ID)->map(function ($filmSeances) {
            return [
                'film_id' => $filmSeances->first()->film_id,
                'titre' => $filmSeances->first()->film_titre,
                'seances_count' => $filmSeances->count(),
                'total_places_disponibles' => $filmSeances->sum(SeancePublicSchema::PLACES_DISPONIBLES),
                'cinemas' => $filmSeances->map(function ($s) {
                    return [
                        'cinema_id' => $s->cinema_id,
                        'cinema_nom' => $s->cinema_nom,
                    ];
                })->unique()->values(),
            ];
        })->sortByDesc('seances_count')->take($limit);

        // ÉTAPE 3: Récupérer les films
        $filmIds = $filmsWithSeances->keys()->toArray();
        $films = $this->filmModel
            ->whereIn(FilmCatalogueSchema::FILM_ID, $filmIds)
            ->where(FilmCatalogueSchema::STATUT_DIFFUSION, 'en_diffusion')
            ->get();

        // ÉTAPE 4: Enrichir
        return $films->map(function ($film) use ($filmsWithSeances) {
            $seanceData = $filmsWithSeances->get($film->film_id);
            if ($seanceData) {
                $film->seances_count = $seanceData['seances_count'];
                $film->total_places_disponibles = $seanceData['total_places_disponibles'];
                $film->cinemas = $seanceData['cinemas'];
            }
            return $film;
        })->sortByDesc('seances_count')->values();
    }

    /**
     * Vérifie si un film a des séances à venir
     */
    public function hasUpcomingSeances(string $filmId): bool
    {
        return $this->seanceModel
            ->where(SeancePublicSchema::FILM_ID, $filmId)
            ->where(SeancePublicSchema::DATE_HEURE_DEBUT, '>=', now())
            ->where(SeancePublicSchema::STATUT, 'PROGRAMMEE')
            ->exists();
    }

    /**
     * Récupère les prochaines séances d'un film
     */
    public function getNextSeances(string $filmId, int $limit = 5): Collection
    {
        return $this->seanceModel
            ->where(SeancePublicSchema::FILM_ID, $filmId)
            ->where(SeancePublicSchema::DATE_HEURE_DEBUT, '>=', now())
            ->where(SeancePublicSchema::STATUT, 'PROGRAMMEE')
            ->orderBy(SeancePublicSchema::DATE_HEURE_DEBUT, 'asc')
            ->limit($limit)
            ->get();
    }

    /**
     * Récupère les statistiques globales pour l'accueil
     */
    public function getHomeStats(): array
    {
        $now = now();
        $weekStart = $now->copy()->startOfWeek(Carbon::WEDNESDAY);
        
        if ($now->dayOfWeek < Carbon::WEDNESDAY) {
            $weekStart->subWeek();
        }
        
        $weekEnd = $weekStart->copy()->addDays(6)->endOfDay();

        // Calculer les statistiques basées sur les séances de la semaine
        $seancesThisWeek = $this->seanceModel
            ->whereBetween(SeancePublicSchema::DATE_HEURE_DEBUT, [$weekStart, $weekEnd])
            ->where(SeancePublicSchema::STATUT, 'PROGRAMMEE')
            ->get([
                SeancePublicSchema::FILM_ID,
                SeancePublicSchema::PLACES_TOTALES,
                SeancePublicSchema::PLACES_DISPONIBLES,
                SeancePublicSchema::CINEMA_ID
            ]);

        // Films uniques cette semaine
        $totalFilms = $seancesThisWeek->pluck(SeancePublicSchema::FILM_ID)->unique()->count();

        // Cinémas uniques cette semaine
        $totalCinemas = $seancesThisWeek->pluck(SeancePublicSchema::CINEMA_ID)->unique()->count();

        // Places totales et vendues
        $totalPlaces = $seancesThisWeek->sum(SeancePublicSchema::PLACES_TOTALES);
        $availablePlaces = $seancesThisWeek->sum(SeancePublicSchema::PLACES_DISPONIBLES);
        $soldPlaces = $totalPlaces - $availablePlaces;

        // Taux de remplissage moyen
        $avgOccupancy = $totalPlaces > 0 ? ($soldPlaces / $totalPlaces) * 100 : 0;

        return [
            'total_films' => $totalFilms,
            'total_cinemas' => $totalCinemas,
            'total_seances' => $seancesThisWeek->count(),
            'total_places' => $totalPlaces,
            'sold_places' => $soldPlaces,
            'available_places' => $availablePlaces,
            'avg_occupancy' => round($avgOccupancy, 1),
        ];
    }

    /**
     * Récupère les statistiques des films pour l'accueil
     */
    public function getFilmStats(): array
    {
        // Récupérer les films en diffusion
        $filmsEnDiffusion = $this->filmModel
            ->where(FilmCatalogueSchema::STATUT_DIFFUSION, 'en_diffusion')
            ->get([
                FilmCatalogueSchema::FILM_ID,
                FilmCatalogueSchema::GENRE,
                FilmCatalogueSchema::NOTE_MOYENNE,
                FilmCatalogueSchema::NOMBRE_AVIS
            ]);

        // Nombre total de films
        $totalFilms = $filmsEnDiffusion->count();

        // Compter les genres uniques
        $genres = $filmsEnDiffusion->pluck(FilmCatalogueSchema::GENRE)
            ->filter(fn($genre) => !empty($genre))
            ->map(fn($genre) => is_array($genre) ? $genre : explode(',', $genre))
            ->flatten()
            ->unique()
            ->count();

        // Note moyenne globale
        $avgRating = $filmsEnDiffusion
            ->filter(fn($film) => $film->note_moyenne > 0)
            ->avg(FilmCatalogueSchema::NOTE_MOYENNE);

        // Total des avis
        $totalReviews = $filmsEnDiffusion->sum(FilmCatalogueSchema::NOMBRE_AVIS);

        return [
            'total_films' => $totalFilms,
            'genres_count' => $genres,
            'avg_rating' => $avgRating > 0 ? round($avgRating, 1) : 0,
            'total_reviews' => $totalReviews,
        ];
    }
}
