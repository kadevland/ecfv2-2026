<?php

declare(strict_types=1);

namespace App\Services\MongoDB;

use Exception;
use MongoDB\Client;
use DateTimeInterface;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use App\Services\MongoPerformanceMonitor;
use Illuminate\Support\Collection as LaravelCollection;
use App\Infrastructure\ReadModel\MongoDB\Collections\ReservationsCollection;

/**
 * Service de requêtage MongoDB pour les réservations avec optimisations avancées
 *
 * Fournit une interface unifiée pour toutes les opérations de lecture sur les réservations
 * avec caching intelligent, précompilation de requêtes et métriques de performance
 */
final readonly class ReservationQueryService
{
    // Cache TTL configurations
    private const CACHE_TTL_SHORT = 300;   // 5 minutes

    private const CACHE_TTL_MEDIUM = 1800; // 30 minutes

    private const CACHE_TTL_LONG = 3600;   // 1 heure

    private ReservationsCollection $reservationsCollection;

    public function __construct(
        private Client $mongoClient,
        private MongoPerformanceMonitor $monitor
    ) {
        $this->reservationsCollection = new ReservationsCollection(
            $mongoClient,
            $monitor
        );
    }

    /**
     * Recherche de réservations avec filtres avancés et cache intelligent
     */
    public function searchReservations(array $filters = [], array $options = []): LaravelCollection
    {
        $cacheKey = $this->generateCacheKey('search_reservations', $filters, $options);

        return Cache::remember($cacheKey, self::CACHE_TTL_MEDIUM, function () use ($filters, $options) {
            $results = $this->reservationsCollection->search($filters, $options);

            return collect($results);
        });
    }

    /**
     * Récupération des réservations par client avec pagination
     */
    public function getReservationsByClient(string $clientId, int $page = 1, int $limit = 20): array
    {
        $cacheKey = "client_reservations_{$clientId}_page_{$page}_limit_{$limit}";

        return Cache::remember($cacheKey, self::CACHE_TTL_SHORT, function () use ($clientId, $page, $limit) {
            $skip = ($page - 1) * $limit;

            $filters = ['client_id' => $clientId];
            $options = [
                'limit' => $limit,
                'skip'  => $skip,
                'sort'  => ['created_at' => -1],
            ];

            $reservations = $this->reservationsCollection->search($filters, $options);

            // Récupérer le total pour pagination
            $total = $this->reservationsCollection->collection->countDocuments(['client_id' => $clientId]);

            return [
                'reservations' => collect($reservations),
                'pagination'   => [
                    'current_page' => $page,
                    'per_page'     => $limit,
                    'total'        => $total,
                    'last_page'    => ceil($total / $limit),
                    'has_more'     => ($page * $limit) < $total,
                ],
            ];
        });
    }

    /**
     * Récupération des réservations par film avec analytics
     */
    public function getReservationsByFilm(string $filmId, ?DateTimeInterface $startDate = null, ?DateTimeInterface $endDate = null): array
    {
        $cacheKey = "film_reservations_{$filmId}_{$startDate?->format('Y-m-d')}_{$endDate?->format('Y-m-d')}";

        return Cache::remember($cacheKey, self::CACHE_TTL_MEDIUM, function () use ($filmId, $startDate, $endDate) {
            $filters = ['film_id' => $filmId];

            if ($startDate || $endDate) {
                $filters['date_from'] = $startDate?->format('Y-m-d');
                $filters['date_to']   = $endDate?->format('Y-m-d');
            }

            $reservations = $this->reservationsCollection->search($filters);

            // Analytics
            $analytics = [
                'total_reservations' => count($reservations),
                'total_revenue'      => array_sum(array_column($reservations, 'prix_total')),
                'total_seats'        => array_sum(array_map(fn ($r) => count($r['places'] ?? []), $reservations)),
                'unique_clients'     => count(array_unique(array_column($reservations, 'client_id'))),
                'average_price'      => 0,
                'occupancy_rate'     => array_sum(array_column($reservations, 'occupancy_rate')) / max(1, count($reservations)),
            ];

            $analytics['average_price'] = $analytics['total_revenue'] / max(1, $analytics['total_seats']);

            return [
                'reservations' => collect($reservations),
                'analytics'    => $analytics,
            ];
        });
    }

    /**
     * Statistiques quotidiennes avec mise en cache
     */
    public function getDailyStats(DateTimeInterface $startDate, DateTimeInterface $endDate): array
    {
        $cacheKey = "daily_stats_{$startDate->format('Y-m-d')}_{$endDate->format('Y-m-d')}";

        return Cache::remember($cacheKey, self::CACHE_TTL_SHORT, function () use ($startDate, $endDate) {
            return $this->reservationsCollection->getDailyStats($startDate, $endDate);
        });
    }

    /**
     * Performance par film avec mise en cache longue
     */
    public function getFilmPerformanceStats(DateTimeInterface $startDate, DateTimeInterface $endDate): array
    {
        $cacheKey = "film_performance_{$startDate->format('Y-m-d')}_{$endDate->format('Y-m-d')}";

        return Cache::remember($cacheKey, self::CACHE_TTL_LONG, function () use ($startDate, $endDate) {
            return $this->reservationsCollection->getFilmPerformanceStats($startDate, $endDate);
        });
    }

    /**
     * Analyse temporelle avancée
     */
    public function getTimeBasedAnalysis(DateTimeInterface $startDate, DateTimeInterface $endDate): array
    {
        $cacheKey = "time_analysis_{$startDate->format('Y-m-d')}_{$endDate->format('Y-m-d')}";

        return Cache::remember($cacheKey, self::CACHE_TTL_MEDIUM, function () use ($startDate, $endDate) {
            return $this->reservationsCollection->getTimeBasedAnalysis($startDate, $endDate);
        });
    }

    /**
     * Performance des cinémas
     */
    public function getCinemaPerformanceStats(DateTimeInterface $startDate, DateTimeInterface $endDate): array
    {
        $cacheKey = "cinema_performance_{$startDate->format('Y-m-d')}_{$endDate->format('Y-m-d')}";

        return Cache::remember($cacheKey, self::CACHE_TTL_LONG, function () use ($startDate, $endDate) {
            return $this->reservationsCollection->getCinemaPerformanceStats($startDate, $endDate);
        });
    }

    /**
     * Segmentation client avancée
     */
    public function getClientSegmentationAnalysis(DateTimeInterface $startDate, DateTimeInterface $endDate): array
    {
        $cacheKey = "client_segmentation_{$startDate->format('Y-m-d')}_{$endDate->format('Y-m-d')}";

        return Cache::remember($cacheKey, self::CACHE_TTL_LONG, function () use ($startDate, $endDate) {
            return $this->reservationsCollection->getClientSegmentationAnalysis($startDate, $endDate);
        });
    }

    /**
     * Analyse d'occupation avec recommendations
     */
    public function getOccupancyAnalysis(DateTimeInterface $startDate, DateTimeInterface $endDate): array
    {
        $cacheKey = "occupancy_analysis_{$startDate->format('Y-m-d')}_{$endDate->format('Y-m-d')}";

        return Cache::remember($cacheKey, self::CACHE_TTL_MEDIUM, function () use ($startDate, $endDate) {
            $analysis = $this->reservationsCollection->getOccupancyAnalysis($startDate, $endDate);

            // Ajouter des recommendations basées sur l'analyse
            if (isset($analysis[0])) {
                $data            = $analysis[0];
                $recommendations = [];

                if ($data['avg_occupancy_rate'] < 50) {
                    $recommendations[] = [
                        'type'       => 'marketing',
                        'priority'   => 'high',
                        'message'    => 'Taux d\'occupation faible (' . round($data['avg_occupancy_rate'], 1) . '%)',
                        'suggestion' => 'Lancer une campagne promotionnelle ou ajuster les prix',
                    ];
                }

                if ($data['low_occupancy_sessions'] > $data['total_sessions'] * 0.3) {
                    $recommendations[] = [
                        'type'       => 'scheduling',
                        'priority'   => 'medium',
                        'message'    => 'Nombre élevé de séances peu fréquentées',
                        'suggestion' => 'Réorganiser les horaires ou regrouper les séances',
                    ];
                }

                if ($data['high_occupancy_sessions'] > $data['total_sessions'] * 0.5) {
                    $recommendations[] = [
                        'type'       => 'expansion',
                        'priority'   => 'medium',
                        'message'    => 'Bon taux d\'occupation sur de nombreuses séances',
                        'suggestion' => 'Considérer l\'ajout de séances supplémentaires',
                    ];
                }

                $data['recommendations'] = $recommendations;
            }

            return $analysis;
        });
    }

    /**
     * Dashboard analytics - Combinaison de plusieurs métriques
     */
    public function getDashboardAnalytics(DateTimeInterface $startDate, DateTimeInterface $endDate): array
    {
        $cacheKey = "dashboard_analytics_{$startDate->format('Y-m-d')}_{$endDate->format('Y-m-d')}";

        return Cache::remember($cacheKey, self::CACHE_TTL_SHORT, function () use ($startDate, $endDate) {
            try {
                // Exécuter plusieurs requêtes en parallèle avec des optimisations
                $dailyStats         = $this->getDailyStats($startDate, $endDate);
                $filmPerformance    = $this->getFilmPerformanceStats($startDate, $endDate);
                $cinemaPerformance  = $this->getCinemaPerformanceStats($startDate, $endDate);
                $timeAnalysis       = $this->getTimeBasedAnalysis($startDate, $endDate);
                $clientSegmentation = $this->getClientSegmentationAnalysis($startDate, $endDate);
                $occupancyAnalysis  = $this->getOccupancyAnalysis($startDate, $endDate);

                // Agréger les données pour le dashboard
                $totalReservations = array_sum(array_column($dailyStats, 'total_reservations'));
                $totalRevenue      = array_sum(array_column($dailyStats, 'total_revenue'));

                return [
                    'summary' => [
                        'total_reservations'   => $totalReservations,
                        'total_revenue'        => $totalRevenue,
                        'average_ticket_price' => $totalReservations > 0 ? $totalRevenue / $totalReservations : 0,
                        'period_days'          => $startDate->diff($endDate)->days + 1,
                        'reservations_per_day' => round($totalReservations / max(1, $startDate->diff($endDate)->days + 1), 1),
                    ],
                    'daily_stats'       => $dailyStats,
                    'top_films'         => array_slice($filmPerformance, 0, 10),
                    'top_cinemas'       => array_slice($cinemaPerformance, 0, 10),
                    'time_patterns'     => $timeAnalysis,
                    'client_segments'   => $clientSegmentation,
                    'occupancy_metrics' => $occupancyAnalysis,
                    'generated_at'      => now()->toISOString(),
                    'cache_status'      => 'fresh',
                ];
            } catch (Exception $e) {
                Log::error('Failed to generate dashboard analytics', [
                    'error'      => $e->getMessage(),
                    'start_date' => $startDate->format('Y-m-d'),
                    'end_date'   => $endDate->format('Y-m-d'),
                ]);

                return [
                    'error'        => 'Unable to generate analytics',
                    'message'      => $e->getMessage(),
                    'generated_at' => now()->toISOString(),
                ];
            }
        });
    }

    /**
     * Recherche en temps réel avec suggestions
     */
    public function realTimeSearch(string $query, array $filters = []): array
    {
        $startTime = microtime(true);

        try {
            // Utiliser l'index textuel pour la recherche
            $searchFilters = array_merge($filters, [
                'search' => $query,
                'limit'  => 20, // Limiter pour performance temps réel
            ]);

            $reservations = $this->reservationsCollection->search($searchFilters);

            // Générer des suggestions basées sur les résultats
            $suggestions = $this->generateSearchSuggestions($reservations, $query);

            $executionTime = microtime(true) - $startTime;

            return [
                'results'        => collect($reservations),
                'suggestions'    => $suggestions,
                'execution_time' => round($executionTime * 1000, 2) . 'ms',
                'total_results'  => count($reservations),
            ];
        } catch (Exception $e) {
            Log::error('Real-time search failed', [
                'query' => $query,
                'error' => $e->getMessage(),
            ]);

            return [
                'results'       => collect([]),
                'suggestions'   => [],
                'error'         => 'Search temporarily unavailable',
                'total_results' => 0,
            ];
        }
    }

    /**
     * Validation de disponibilité des places en temps réel
     */
    public function checkSeatAvailability(string $seanceId, int $requestedSeats): array
    {
        $cacheKey = "seat_availability_{$seanceId}";

        // Cache très court pour disponibilité temps réel
        return Cache::remember($cacheKey, 30, function () use ($seanceId, $requestedSeats) {
            try {
                // Compter les places déjà réservées pour cette séance
                $pipeline = [
                    ['$match' => ['seance_id' => $seanceId, 'statut' => ['$in' => ['confirmee', 'payee']]]],
                    ['$group' => [
                        '_id'                => '$seance_id',
                        'reserved_seats'     => ['$sum' => ['$size' => '$places']],
                        'total_reservations' => ['$sum' => 1],
                    ]],
                ];

                $result = $this->reservationsCollection->aggregate($pipeline);
                $data   = $result[0] ?? ['reserved_seats' => 0, 'total_reservations' => 0];

                // Récupérer la capacité de la salle (à optimiser avec cache)
                $salleCapacity  = $this->getSalleCapacity($seanceId);
                $availableSeats = $salleCapacity - $data['reserved_seats'];

                return [
                    'seance_id'          => $seanceId,
                    'salle_capacity'     => $salleCapacity,
                    'reserved_seats'     => $data['reserved_seats'],
                    'available_seats'    => $availableSeats,
                    'requested_seats'    => $requestedSeats,
                    'is_available'       => $availableSeats >= $requestedSeats,
                    'occupancy_rate'     => round(($data['reserved_seats'] / $salleCapacity) * 100, 1),
                    'total_reservations' => $data['total_reservations'],
                ];
            } catch (Exception $e) {
                Log::error('Seat availability check failed', [
                    'seance_id' => $seanceId,
                    'error'     => $e->getMessage(),
                ]);

                return [
                    'seance_id'    => $seanceId,
                    'is_available' => false,
                    'error'        => 'Unable to check seat availability',
                ];
            }
        });
    }

    /**
     * Optimisation des requêtes fréquentes avec pré-compilation
     */
    public function getPopularFilms(int $limit = 10, ?DateTimeInterface $startDate = null, ?DateTimeInterface $endDate = null): array
    {
        $cacheKey = "popular_films_{$limit}_{$startDate?->format('Y-m-d')}_{$endDate?->format('Y-m-d')}";

        return Cache::remember($cacheKey, self::CACHE_TTL_LONG, function () use ($limit, $startDate, $endDate) {
            $pipeline = [
                [
                    '$match' => array_filter([
                        'statut'      => ['$in' => ['confirmee', 'payee']],
                        'date_seance' => ($startDate && $endDate) ? [
                            '$gte' => new \MongoDB\BSON\UTCDateTime($startDate->getTimestamp() * 1000),
                            '$lte' => new \MongoDB\BSON\UTCDateTime($endDate->getTimestamp() * 1000),
                        ] : null,
                    ]),
                ],
                [
                    '$group' => [
                        '_id'            => ['film_id' => '$film_id', 'film_titre' => '$film_titre'],
                        'reservations'   => ['$sum' => 1],
                        'revenue'        => ['$sum' => '$prix_total'],
                        'unique_clients' => ['$addToSet' => '$client_id'],
                    ],
                ],
                [
                    '$addFields' => [
                        'unique_clients_count' => ['$size' => '$unique_clients'],
                        'popularity_score'     => [
                            '$add' => [
                                ['$multiply' => ['$reservations', 1]],
                                ['$multiply' => ['$unique_clients_count', 2]],
                                ['$divide'   => ['$revenue', 100]],
                            ],
                        ],
                    ],
                ],
                ['$sort'  => ['popularity_score' => -1]],
                ['$limit' => $limit],
            ];

            return $this->reservationsCollection->aggregate($pipeline);
        });
    }

    /**
     * Nettoyage des caches pour maintenance
     */
    public function clearCaches(array $patterns = []): void
    {
        $allPatterns = array_merge($patterns, [
            'search_reservations_*',
            'daily_stats_*',
            'film_performance_*',
            'time_analysis_*',
            'cinema_performance_*',
            'client_segmentation_*',
            'occupancy_analysis_*',
            'dashboard_analytics_*',
            'popular_films_*',
            'seat_availability_*',
        ]);

        foreach ($allPatterns as $pattern) {
            Cache::forget($pattern);
        }

        Log::info('MongoDB query service caches cleared', ['patterns' => $allPatterns]);
    }

    /**
     * Diagnostic de performance des requêtes
     */
    public function diagnosePerformance(): array
    {
        $startTime = microtime(true);

        try {
            // Test de performance basique
            $testQueries = [
                'simple_search'       => fn () => $this->reservationsCollection->search([], ['limit' => 10]),
                'complex_aggregation' => fn () => $this->reservationsCollection->getDailyStats(
                    now()->subDays(7),
                    now()
                ),
                'text_search' => fn () => $this->reservationsCollection->search(['search' => 'film'], ['limit' => 5]),
            ];

            $results = [];
            foreach ($testQueries as $name => $query) {
                $queryStart = microtime(true);
                $query();
                $results[$name] = round((microtime(true) - $queryStart) * 1000, 2) . 'ms';
            }

            return [
                'status'               => 'healthy',
                'query_performance'    => $results,
                'total_diagnosis_time' => round((microtime(true) - $startTime) * 1000, 2) . 'ms',
                'cache_status'         => 'enabled',
                'recommendations'      => $this->generatePerformanceRecommendations($results),
            ];
        } catch (Exception $e) {
            return [
                'status'               => 'error',
                'error'                => $e->getMessage(),
                'total_diagnosis_time' => round((microtime(true) - $startTime) * 1000, 2) . 'ms',
            ];
        }
    }

    /**
     * Méthodes privées helpers
     */
    private function generateCacheKey(string $prefix, array $filters, array $options): string
    {
        $hash = md5(serialize($filters) . serialize($options));

        return "{$prefix}_{$hash}";
    }

    private function generateSearchSuggestions(array $results, string $query): array
    {
        $suggestions = [];

        if (empty($results)) {
            return $suggestions;
        }

        // Extraire les titres de films uniques
        $films   = array_unique(array_column($results, 'film_titre'));
        $cinemas = array_unique(array_column($results, 'cinema_nom'));

        // Générer des suggestions basées sur les correspondances partielles
        foreach ($films as $film) {
            if (stripos($film, $query) !== false && $film !== $query) {
                $suggestions[] = ['type' => 'film', 'value' => $film];
            }
        }

        foreach ($cinemas as $cinema) {
            if (stripos($cinema, $query) !== false && $cinema !== $query) {
                $suggestions[] = ['type' => 'cinema', 'value' => $cinema];
            }
        }

        return array_slice($suggestions, 0, 5); // Limiter à 5 suggestions
    }

    private function getSalleCapacity(string $seanceId): int
    {
        // Cette méthode pourrait être optimisée avec un cache dédié
        // pour l'instant, retourne une valeur par défaut
        $cacheKey = "salle_capacity_{$seanceId}";

        return Cache::remember($cacheKey, 3600, function () {
            // Logique pour récupérer la capacité depuis MongoDB ou une autre source
            // Pour l'instant, retourne une valeur par défaut
            return 100;
        });
    }

    private function generatePerformanceRecommendations(array $queryResults): array
    {
        $recommendations = [];

        foreach ($queryResults as $query => $time) {
            $timeMs = (float) str_replace('ms', '', $time);

            if ($timeMs > 1000) {
                $recommendations[] = [
                    'query'      => $query,
                    'issue'      => 'slow_query',
                    'message'    => "Query {$query} took {$time}",
                    'suggestion' => 'Consider optimizing indexes or query structure',
                ];
            }
        }

        return $recommendations;
    }
}
