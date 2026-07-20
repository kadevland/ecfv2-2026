<?php

declare(strict_types=1);

namespace App\Services\MongoDB;

use Closure;
use MongoDB\BSON\UTCDateTime;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Infrastructure\Database\ReadModels\FilmCatalogue;
use App\Infrastructure\Database\Schemas\Cinema\FilmCatalogueSchema;

/**
 * Service pour optimiser les requêtes MongoDB sur le catalogue films
 * Centralise la logique métier et les requêtes complexes
 */
class FilmCatalogueQueryService
{
    public function __construct(
        private FilmCatalogue $model
    ) {}

    /**
     * Recherche unifiée avec cache et optimisations
     *
     * @param array<string, mixed> $criteria
     * @return Collection<int, FilmCatalogue>
     */
    public function searchUnified(array $criteria): Collection
    {
        $query = $this->model->enDiffusion();

        // Application des critères de recherche
        $this->applyCriteria($query, $criteria);

        // Application du tri
        $this->applySorting($query, $criteria['sort'] ?? 'recent');

        // Limitation des résultats
        $limit = $criteria['limit'] ?? 20;

        return $query->limit($limit)->get();
    }

    /**
     * Recherche paginée optimisée
     *
     * @param array<string, mixed> $criteria
     * @return LengthAwarePaginator<FilmCatalogue>
     */
    public function searchPaginated(array $criteria, int $perPage = 20): LengthAwarePaginator
    {
        $query = $this->model->enDiffusion();

        // Application des critères
        $this->applyCriteria($query, $criteria);

        // Application du tri
        $this->applySorting($query, $criteria['sort'] ?? 'recent');

        return $query->paginate($perPage);
    }

    /**
     * Obtient les films tendances avec métriques avancées
     *
     * @return Collection<int, mixed>
     */
    public function getTrendingFilms(int $days = 7, int $limit = 10): Collection
    {
        $startDate = now()->subDays($days);

        $result = $this->model::raw(function ($collection) use ($startDate, $limit) {
            return $collection->aggregate([
                [
                    '$match' => [
                        FilmCatalogueSchema::STATUT_DIFFUSION                         => 'en_diffusion',
                        FilmCatalogueSchema::PROCHAINES_SEANCES . '.date_heure_debut' => [
                            '$gte' => new UTCDateTime($startDate->getTimestamp() * 1000),
                        ],
                    ],
                ],
                [
                    '$addFields' => [
                        'trending_score' => [
                            '$multiply' => [
                                '$' . FilmCatalogueSchema::NOTE_MOYENNE,
                                ['$size' => '$' . FilmCatalogueSchema::PROCHAINES_SEANCES],
                                ['$sqrt' => '$' . FilmCatalogueSchema::NOMBRE_AVIS],
                            ],
                        ],
                        'seances_this_week' => [
                            '$size' => [
                                '$filter' => [
                                    'input' => '$' . FilmCatalogueSchema::PROCHAINES_SEANCES,
                                    'cond'  => [
                                        '$gte' => [
                                            '$$this.date_heure_debut',
                                            new UTCDateTime($startDate->getTimestamp() * 1000),
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
                [
                    '$match' => [
                        'seances_this_week' => ['$gte' => 1],
                    ],
                ],
                [
                    '$sort' => ['trending_score' => -1],
                ],
                [
                    '$limit' => $limit,
                ],
            ]);
        });

        return collect($result);
    }

    /**
     * Analyse des préférences utilisateur et recommandations
     *
     * @param array<string, mixed> $userPreferences
     * @return Collection<int, mixed>
     */
    public function getPersonalizedRecommendations(array $userPreferences, int $limit = 10): Collection
    {
        $genreWeights = $userPreferences['genres'] ?? [];
        $minRating    = $userPreferences['min_rating'] ?? 3.0;
        $maxDuration  = $userPreferences['max_duration'] ?? 180;

        $result = $this->model::raw(function ($collection) use ($genreWeights, $minRating, $maxDuration, $limit) {
            $pipeline = [
                [
                    '$match' => [
                        FilmCatalogueSchema::STATUT_DIFFUSION => 'en_diffusion',
                        FilmCatalogueSchema::NOTE_MOYENNE     => ['$gte' => $minRating],
                        FilmCatalogueSchema::DUREE            => ['$lte' => $maxDuration],
                    ],
                ],
            ];

            // Ajout du score de recommandation basé sur les genres préférés
            if (!empty($genreWeights)) {
                $genreConditions = [];
                foreach ($genreWeights as $genre => $weight) {
                    $genreConditions[] = [
                        '$cond' => [
                            ['$eq' => ['$' . FilmCatalogueSchema::GENRE, $genre]],
                            $weight,
                            0,
                        ],
                    ];
                }

                $pipeline[] = [
                    '$addFields' => [
                        'recommendation_score' => [
                            '$add' => array_merge($genreConditions, [
                                ['$multiply' => ['$' . FilmCatalogueSchema::NOTE_MOYENNE, 2]],
                                ['$divide' => ['$' . FilmCatalogueSchema::NOMBRE_AVIS, 100]],
                            ]),
                        ],
                    ],
                ];

                $pipeline[] = ['$sort' => ['recommendation_score' => -1]];
            } else {
                $pipeline[] = ['$sort' => [FilmCatalogueSchema::NOTE_MOYENNE => -1]];
            }

            $pipeline[] = ['$limit' => $limit];

            return $collection->aggregate($pipeline);
        });

        return collect($result);
    }

    /**
     * Statistiques avancées du catalogue
     *
     * @return array<string, mixed>
     */
    public function getCatalogueAnalytics(): array
    {
        $genreStats = $this->model::getGenreStats()->toArray();

        $ratingDistribution = $this->model::raw(function ($collection) {
            return $collection->aggregate([
                [
                    '$match' => [FilmCatalogueSchema::STATUT_DIFFUSION => 'en_diffusion'],
                ],
                [
                    '$group' => [
                        '_id' => [
                            '$floor' => ['$' . FilmCatalogueSchema::NOTE_MOYENNE],
                        ],
                        'count' => ['$sum' => 1],
                    ],
                ],
                [
                    '$sort' => ['_id' => 1],
                ],
            ]);
        });

        $durationStats = $this->model::raw(function ($collection) {
            return $collection->aggregate([
                [
                    '$match' => [FilmCatalogueSchema::STATUT_DIFFUSION => 'en_diffusion'],
                ],
                [
                    '$group' => [
                        '_id'          => null,
                        'avg_duration' => ['$avg' => '$' . FilmCatalogueSchema::DUREE],
                        'min_duration' => ['$min' => '$' . FilmCatalogueSchema::DUREE],
                        'max_duration' => ['$max' => '$' . FilmCatalogueSchema::DUREE],
                        'total_films'  => ['$sum' => 1],
                    ],
                ],
            ]);
        });

        return [
            'genre_stats'         => $genreStats,
            'rating_distribution' => collect($ratingDistribution)->toArray(),
            'duration_stats'      => collect($durationStats)->first(),
            'generated_at'        => now()->toISOString(),
        ];
    }

    /**
     * Recherche géolocalisée par proximité de cinéma
     *
     * @return Collection<int, FilmCatalogue>
     */
    public function findNearbyFilms(float $latitude, float $longitude, float $radiusKm = 10): Collection
    {
        // Pour l'instant, recherche basique par ville
        // À améliorer avec la géolocalisation réelle des cinémas
        return $this->model->enDiffusion()
            ->withAvailableSeances()
            ->popular()
            ->limit(20)
            ->get();
    }

    /**
     * Recherche de créneaux libres pour un film
     *
     * @return Collection<int, mixed>
     */
    public function findAvailableSlots(string $filmId, string $date, ?string $cinemaId = null): Collection
    {
        $query = $this->model->where(FilmCatalogueSchema::FILM_ID, $filmId);

        if ($cinemaId) {
            $query->byCinema($cinemaId);
        }

        $film = $query->first();

        if (!$film) {
            return collect();
        }

        // Filtrer les séances pour la date donnée
        $seances = collect($film->prochaines_seances)
            ->filter(function ($seance) use ($date) {
                $seanceDate = \Carbon\Carbon::parse($seance['date_heure_debut'])->format('Y-m-d');

                return $seanceDate === $date;
            })
            ->values();

        return $seances;
    }

    /**
     * Mise en cache intelligente pour les requêtes fréquentes
     */
    public function getCachedStats(string $key, Closure $callback, int $ttlMinutes = 60): mixed
    {
        $cacheKey = "film_catalogue_stats:{$key}";

        return cache()->remember($cacheKey, $ttlMinutes * 60, $callback);
    }

    /**
     * Invalidation du cache lors de modifications
     */
    public function clearStatsCache(): void
    {
        $keys = [
            'film_catalogue_stats:genre_stats',
            'film_catalogue_stats:trending',
            'film_catalogue_stats:analytics',
        ];

        foreach ($keys as $key) {
            cache()->forget($key);
        }
    }

    /**
     * Application des critères de recherche sur une query
     *
     * @param array<string, mixed> $criteria
     */
    private function applyCriteria(mixed $query, array $criteria): void
    {
        if (!empty($criteria['genres'])) {
            $query->whereIn(FilmCatalogueSchema::GENRE, $criteria['genres']);
        }

        if (!empty($criteria['classifications'])) {
            $query->whereIn(FilmCatalogueSchema::CLASSIFICATION, $criteria['classifications']);
        }

        if (isset($criteria['note_min'])) {
            $query->minNote((float) $criteria['note_min']);
        }

        if (isset($criteria['note_max'])) {
            $query->where(FilmCatalogueSchema::NOTE_MOYENNE, '<=', (float) $criteria['note_max']);
        }

        if (isset($criteria['duree_min']) && isset($criteria['duree_max'])) {
            $query->durationBetween((int) $criteria['duree_min'], (int) $criteria['duree_max']);
        }

        if (!empty($criteria['cinema_ids'])) {
            $query->inCinemas($criteria['cinema_ids']);
        }

        if (!empty($criteria['ville'])) {
            $query->inCity($criteria['ville']);
        }

        if (!empty($criteria['search'])) {
            $query->searchAdvanced($criteria['search']);
        }

        if (!empty($criteria['director'])) {
            $query->byDirector($criteria['director']);
        }

        if (!empty($criteria['actor'])) {
            $query->withActor($criteria['actor']);
        }

        if (isset($criteria['min_reviews'])) {
            $query->minReviews((int) $criteria['min_reviews']);
        }

        if ($criteria['with_seances'] ?? false) {
            $query->withAvailableSeances();
        }
    }

    /**
     * Application du tri sur une query
     */
    private function applySorting(mixed $query, string $sort): void
    {
        match ($sort) {
            'popular'       => $query->popular(),
            'note'          => $query->orderBy(FilmCatalogueSchema::NOTE_MOYENNE, 'desc'),
            'title'         => $query->orderBy(FilmCatalogueSchema::TITRE, 'asc'),
            'duration_asc'  => $query->orderBy(FilmCatalogueSchema::DUREE, 'asc'),
            'duration_desc' => $query->orderBy(FilmCatalogueSchema::DUREE, 'desc'),
            'release_date'  => $query->orderBy(FilmCatalogueSchema::DATE_SORTIE, 'desc'),
            default         => $query->recent()
        };
    }
}
