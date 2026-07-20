<?php

declare(strict_types=1);

namespace App\Infrastructure\ReadModel\MongoDB\Collections;

use Exception;
use MongoDB\Client;
use DateTimeInterface;
use MongoDB\BSON\ObjectId;
use MongoDB\BSON\UTCDateTime;
use Illuminate\Support\Facades\Log;
use App\Services\MongoPerformanceMonitor;
use MongoDB\Laravel\Collection as MongoCollection;

/**
 * Collection MongoDB optimisée pour les réservations
 *
 * Architecture CQRS: Collection de lecture (read-side) avec:
 * - Schéma dénormalisé pour performances optimales
 * - Indexation stratégique pour queries rapides
 * - Pipelines d'agrégation pour statistiques business
 * - Monitoring et métriques intégrées
 */
final readonly class ReservationsCollection
{
    private MongoCollection $collection;

    private MongoPerformanceMonitor $monitor;

    public function __construct(
        private Client $client,
        MongoPerformanceMonitor $monitor,
        string $database = 'cinephoria_read',
        string $collectionName = 'reservations'
    ) {
        $this->collection = $client->selectCollection($database, $collectionName);
        $this->monitor    = $monitor;
    }

    /**
     * Crée une réservation dans MongoDB (CQRS - Read Model)
     * Complexité: O(1) avec indexation appropriée
     */
    public function create(array $data): string
    {
        $startTime = microtime(true);

        try {
            $document = $this->prepareReservationDocument($data);

            $result = $this->collection->insertOne($document);

            $this->monitorPerformance([
                'operation'      => 'create',
                'collection'     => 'reservations',
                'execution_time' => microtime(true) - $startTime,
                'document_count' => 1,
            ]);

            Log::info('Reservation created in MongoDB', [
                'reservation_id' => $data['reservation_id'] ?? null,
                'mongodb_id'     => $result->getInsertedId()->__toString(),
            ]);

            return $result->getInsertedId()->__toString();
        } catch (Exception $e) {
            Log::error('Failed to create reservation in MongoDB', [
                'error' => $e->getMessage(),
                'data'  => $data,
            ]);
            throw $e;
        }
    }

    /**
     * Mise à jour de réservation avec upsert automatique
     */
    public function update(string $reservationId, array $updates): bool
    {
        $startTime = microtime(true);

        try {
            $result = $this->collection->updateOne(
                ['reservation_id' => $reservationId],
                [
                    '$set' => array_merge($updates, [
                        'updated_at' => new UTCDateTime,
                        'last_sync'  => new UTCDateTime,
                    ]),
                ],
                ['upsert' => false]
            );

            $this->monitorPerformance([
                'operation'      => 'update',
                'collection'     => 'reservations',
                'execution_time' => microtime(true) - $startTime,
                'document_count' => 1,
                'matched_count'  => $result->getMatchedCount(),
            ]);

            return $result->getModifiedCount() > 0;
        } catch (Exception $e) {
            Log::error('Failed to update reservation in MongoDB', [
                'reservation_id' => $reservationId,
                'error'          => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Synchronisation en masse - Optimisation pour gros volumes
     */
    public function bulkUpsert(array $reservations): array
    {
        $startTime   = microtime(true);
        $memoryStart = memory_get_usage(true);

        try {
            $operations = [];
            foreach ($reservations as $reservation) {
                $document = $this->prepareReservationDocument($reservation);

                $operations[] = [
                    'updateOne' => [
                        ['filter' => ['reservation_id' => $reservation['reservation_id']]],
                        ['update' => ['$set' => $document], 'upsert' => true],
                    ],
                ];
            }

            // Traiter par lots de 1000 pour éviter les timeouts
            $batchSize = 1000;
            $results   = [
                'total_processed' => 0,
                'upserted_count'  => 0,
                'modified_count'  => 0,
                'execution_time'  => 0,
            ];

            for ($i = 0; $i < count($operations); $i += $batchSize) {
                $batch       = array_slice($operations, $i, $batchSize);
                $batchResult = $this->collection->bulkWrite($batch);

                $results['total_processed'] += count($batch);
                $results['upserted_count'] += $batchResult->getUpsertedCount();
                $results['modified_count'] += $batchResult->getModifiedCount();
            }

            $executionTime = microtime(true) - $startTime;
            $memoryUsage   = memory_get_usage(true) - $memoryStart;

            $this->monitorPerformance([
                'operation'      => 'bulk_upsert',
                'collection'     => 'reservations',
                'execution_time' => $executionTime,
                'document_count' => count($reservations),
                'memory_usage'   => $memoryUsage,
                'throughput'     => count($reservations) / $executionTime,
            ]);

            $results['execution_time'] = $executionTime;
            $results['throughput']     = count($reservations) / $executionTime;

            return $results;
        } catch (Exception $e) {
            Log::error('Failed bulk upsert reservations in MongoDB', [
                'error'             => $e->getMessage(),
                'reservation_count' => count($reservations),
            ]);
            throw $e;
        }
    }

    /**
     * Recherche de réservations avec filtres avancés
     */
    public function search(array $filters = [], array $options = []): array
    {
        $startTime = microtime(true);

        try {
            $query  = $this->buildSearchQuery($filters);
            $cursor = $this->collection->find($query, $this->buildSearchOptions($options));

            $reservations = [];
            foreach ($cursor as $document) {
                $reservations[] = $this->transformDocumentToArray($document);
            }

            $this->monitorPerformance([
                'operation'      => 'search',
                'collection'     => 'reservations',
                'execution_time' => microtime(true) - $startTime,
                'result_count'   => count($reservations),
            ]);

            return $reservations;
        } catch (Exception $e) {
            Log::error('Failed to search reservations in MongoDB', [
                'error'   => $e->getMessage(),
                'filters' => $filters,
            ]);
            throw $e;
        }
    }

    /**
     * Pipeline d'agrégation - Statistiques quotidiennes
     * Analyse des performances par jour
     */
    public function getDailyStats(DateTimeInterface $startDate, DateTimeInterface $endDate): array
    {
        $pipeline = [
            [
                '$match' => [
                    'date_seance' => [
                        '$gte' => new UTCDateTime($startDate->getTimestamp() * 1000),
                        '$lte' => new UTCDateTime($endDate->getTimestamp() * 1000),
                    ],
                ],
            ],
            [
                '$group' => [
                    '_id' => [
                        'date'   => ['$dateToString' => ['format' => '%Y-%m-%d', 'date' => '$date_seance']],
                        'statut' => '$statut',
                    ],
                    'count'        => ['$sum' => 1],
                    'total_amount' => ['$sum' => '$prix_total'],
                    'total_seats'  => ['$sum' => ['$size' => '$places']],
                    'avg_amount'   => ['$avg' => '$prix_total'],
                ],
            ],
            [
                '$group' => [
                    '_id'                => '$_id.date',
                    'total_reservations' => ['$sum' => '$count'],
                    'total_revenue'      => ['$sum' => '$total_amount'],
                    'total_seats'        => ['$sum' => '$total_seats'],
                    'avg_ticket_price'   => ['$avg' => '$avg_amount'],
                    'statuses'           => [
                        '$push' => [
                            'statut' => '$_id.statut',
                            'count'  => '$count',
                        ],
                    ],
                ],
            ],
            ['$sort' => ['_id' => 1]],
        ];

        return $this->aggregate($pipeline);
    }

    /**
     * Pipeline d'agrégation - Performance par film
     * Analyse des films les plus populaires et rentables
     */
    public function getFilmPerformanceStats(DateTimeInterface $startDate, DateTimeInterface $endDate): array
    {
        $pipeline = [
            [
                '$match' => [
                    'date_seance' => [
                        '$gte' => new UTCDateTime($startDate->getTimestamp() * 1000),
                        '$lte' => new UTCDateTime($endDate->getTimestamp() * 1000),
                    ],
                    'statut' => ['$in' => ['confirmee', 'payee']],
                ],
            ],
            [
                '$group' => [
                    '_id' => [
                        'film_id'    => '$film_id',
                        'film_titre' => '$film_titre',
                    ],
                    'total_reservations'   => ['$sum' => 1],
                    'total_revenue'        => ['$sum' => '$prix_total'],
                    'total_seats'          => ['$sum' => ['$size' => '$places']],
                    'unique_clients'       => ['$addToSet' => '$client_id'],
                    'avg_price_per_ticket' => ['$avg' => ['$divide' => ['$prix_total', ['$size' => '$places']]]],
                    'occupancy_rate'       => ['$avg' => '$occupancy_rate'],
                ],
            ],
            [
                '$addFields' => [
                    'unique_clients_count' => ['$size' => '$unique_clients'],
                ],
            ],
            [
                '$project' => [
                    'unique_clients' => 0, // Optimisation mémoire
                ],
            ],
            ['$sort' => ['total_revenue' => -1]],
        ];

        return $this->aggregate($pipeline);
    }

    /**
     * Pipeline d'agrégation - Analyse temporelle avancée
     * Patterns de réservation par heure, jour de semaine, etc.
     */
    public function getTimeBasedAnalysis(DateTimeInterface $startDate, DateTimeInterface $endDate): array
    {
        $pipeline = [
            [
                '$match' => [
                    'created_at' => [
                        '$gte' => new UTCDateTime($startDate->getTimestamp() * 1000),
                        '$lte' => new UTCDateTime($endDate->getTimestamp() * 1000),
                    ],
                ],
            ],
            [
                '$addFields' => [
                    'hour_of_day'  => ['$hour' => '$created_at'],
                    'day_of_week'  => ['$dayOfWeek' => '$created_at'],
                    'day_of_month' => ['$dayOfMonth' => '$created_at'],
                    'month'        => ['$month' => '$created_at'],
                    'year'         => ['$year' => '$created_at'],
                ],
            ],
            [
                '$facet' => [
                    'hourly_distribution' => [
                        ['$group' => [
                            '_id'     => '$hour_of_day',
                            'count'   => ['$sum' => 1],
                            'revenue' => ['$sum' => '$prix_total'],
                        ]],
                        ['$sort' => ['_id' => 1]],
                    ],
                    'daily_distribution' => [
                        ['$group' => [
                            '_id'      => '$day_of_week',
                            'count'    => ['$sum' => 1],
                            'revenue'  => ['$sum' => '$prix_total'],
                            'day_name' => ['$arrayElemAt' => [
                                ['', 'Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'],
                                '$_id',
                            ]],
                        ]],
                        ['$sort' => ['count' => -1]],
                    ],
                    'monthly_trends' => [
                        ['$group' => [
                            '_id'              => ['year' => '$year', 'month' => '$month'],
                            'count'            => ['$sum' => 1],
                            'revenue'          => ['$sum' => '$prix_total'],
                            'avg_ticket_price' => ['$avg' => '$prix_total'],
                        ]],
                        ['$sort' => ['_id.year' => 1, '_id.month' => 1]],
                    ],
                ],
            ],
        ];

        return $this->aggregate($pipeline);
    }

    /**
     * Pipeline d'agrégation - Performance des cinémas
     */
    public function getCinemaPerformanceStats(DateTimeInterface $startDate, DateTimeInterface $endDate): array
    {
        $pipeline = [
            [
                '$match' => [
                    'date_seance' => [
                        '$gte' => new UTCDateTime($startDate->getTimestamp() * 1000),
                        '$lte' => new UTCDateTime($endDate->getTimestamp() * 1000),
                    ],
                ],
            ],
            [
                '$group' => [
                    '_id' => [
                        'cinema_id'  => '$cinema_id',
                        'cinema_nom' => '$cinema_nom',
                    ],
                    'total_reservations' => ['$sum' => 1],
                    'total_revenue'      => ['$sum' => '$prix_total'],
                    'unique_films'       => ['$addToSet' => '$film_id'],
                    'unique_clients'     => ['$addToSet' => '$client_id'],
                    'avg_ticket_price'   => ['$avg' => '$prix_total'],
                ],
            ],
            [
                '$addFields' => [
                    'unique_films_count'   => ['$size' => '$unique_films'],
                    'unique_clients_count' => ['$size' => '$unique_clients'],
                ],
            ],
            [
                '$project' => [
                    'unique_films'   => 0,
                    'unique_clients' => 0,
                ],
            ],
            ['$sort' => ['total_revenue' => -1]],
        ];

        return $this->aggregate($pipeline);
    }

    /**
     * Pipeline d'agrégation - Analyse client avancée
     * Segmentation, fidélité, patterns d'achat
     */
    public function getClientSegmentationAnalysis(DateTimeInterface $startDate, DateTimeInterface $endDate): array
    {
        $pipeline = [
            [
                '$match' => [
                    'created_at' => [
                        '$gte' => new UTCDateTime($startDate->getTimestamp() * 1000),
                        '$lte' => new UTCDateTime($endDate->getTimestamp() * 1000),
                    ],
                    'client_id' => ['$exists' => true],
                ],
            ],
            [
                '$group' => [
                    '_id'                => '$client_id',
                    'total_reservations' => ['$sum' => 1],
                    'total_spent'        => ['$sum' => '$prix_total'],
                    'first_reservation'  => ['$min' => '$created_at'],
                    'last_reservation'   => ['$max' => '$created_at'],
                    'unique_films'       => ['$addToSet' => '$film_id'],
                    'unique_cinemas'     => ['$addToSet' => '$cinema_id'],
                    'avg_ticket_price'   => ['$avg' => '$prix_total'],
                    'preferred_days'     => ['$push' => ['$dayOfWeek' => '$date_seance']],
                ],
            ],
            [
                '$addFields' => [
                    'unique_films_count'        => ['$size' => '$unique_films'],
                    'unique_cinemas_count'      => ['$size' => '$unique_cinemas'],
                    'days_between_reservations' => [
                        '$divide' => [
                            ['$subtract' => ['$last_reservation', '$first_reservation']],
                            ['$multiply' => ['$total_reservations', 24 * 60 * 60 * 1000]],
                        ],
                    ],
                    'segment' => [
                        '$switch' => [
                            'branches' => [
                                ['case' => ['$gte' => ['$total_reservations', 20]], 'then' => 'VIP'],
                                ['case' => ['$gte' => ['$total_reservations', 10]], 'then' => 'Fidèle'],
                                ['case' => ['$gte' => ['$total_reservations', 5]], 'then' => 'Régulier'],
                                ['case' => ['$gte' => ['$total_reservations', 2]], 'then' => 'Occasionnel'],
                            ],
                            'default' => 'Nouveau',
                        ],
                    ],
                ],
            ],
            [
                '$group' => [
                    '_id'                => '$segment',
                    'client_count'       => ['$sum' => 1],
                    'avg_reservations'   => ['$avg' => '$total_reservations'],
                    'avg_spent'          => ['$avg' => '$total_spent'],
                    'total_revenue'      => ['$sum' => '$total_spent'],
                    'revenue_percentage' => [
                        '$multiply' => [
                            100,
                            ['$divide' => ['$total_spent', ['$sum' => '$total_spent']]],
                        ],
                    ],
                ],
            ],
            ['$sort' => ['total_revenue' => -1]],
        ];

        return $this->aggregate($pipeline);
    }

    /**
     * Pipeline d'agrégation - Taux d'occupation et optimisation
     */
    public function getOccupancyAnalysis(DateTimeInterface $startDate, DateTimeInterface $endDate): array
    {
        $pipeline = [
            [
                '$match' => [
                    'date_seance' => [
                        '$gte' => new UTCDateTime($startDate->getTimestamp() * 1000),
                        '$lte' => new UTCDateTime($endDate->getTimestamp() * 1000),
                    ],
                ],
            ],
            [
                '$group' => [
                    '_id' => [
                        'seance_id'    => '$seance_id',
                        'film_titre'   => '$film_titre',
                        'cinema_nom'   => '$cinema_nom',
                        'heure_seance' => '$heure_seance',
                    ],
                    'total_seats_reserved' => ['$sum' => ['$size' => '$places']],
                    'total_reservations'   => ['$sum' => 1],
                    'max_capacity'         => ['$first' => '$salle_capacity'],
                    'total_revenue'        => ['$sum' => '$prix_total'],
                ],
            ],
            [
                '$addFields' => [
                    'occupancy_rate' => [
                        '$multiply' => [
                            100,
                            ['$divide' => ['$total_seats_reserved', '$max_capacity']],
                        ],
                    ],
                    'revenue_per_seat' => [
                        '$divide' => ['$total_revenue', '$total_seats_reserved'],
                    ],
                ],
            ],
            [
                '$group' => [
                    '_id'                     => null,
                    'avg_occupancy_rate'      => ['$avg' => '$occupancy_rate'],
                    'max_occupancy_rate'      => ['$max' => '$occupancy_rate'],
                    'min_occupancy_rate'      => ['$min' => '$occupancy_rate'],
                    'total_sessions'          => ['$sum' => 1],
                    'high_occupancy_sessions' => [
                        '$sum' => ['$cond' => [['$gte' => ['$occupancy_rate', 80]], 1, 0]],
                    ],
                    'low_occupancy_sessions' => [
                        '$sum' => ['$cond' => [['$lte' => ['$occupancy_rate', 30]], 1, 0]],
                    ],
                    'occupancy_distribution' => [
                        '$push' => [
                            'occupancy_rate' => '$occupancy_rate',
                            'film_titre'     => '$_id.film_titre',
                            'cinema_nom'     => '$_id.cinema_nom',
                        ],
                    ],
                ],
            ],
        ];

        return $this->aggregate($pipeline);
    }

    /**
     * Création des indexes optimisés pour la collection
     */
    public function createIndexes(): void
    {
        $indexes = [
            // Index primaire sur reservation_id
            ['key' => ['reservation_id' => 1], 'unique' => true],

            // Index composé pour recherche par client et statut
            ['key' => ['client_id' => 1, 'statut' => 1]],

            // Index composé pour recherche par film et date
            ['key' => ['film_id' => 1, 'date_seance' => -1]],

            // Index composé pour recherche par cinéma et date
            ['key' => ['cinema_id' => 1, 'date_seance' => -1]],

            // Index temporel pour analyse des tendances
            ['key' => ['created_at' => -1]],

            // Index pour recherche par numéro de réservation
            ['key' => ['numero_confirmation' => 1], 'unique' => true, 'sparse' => true],

            // Index pour recherche QR code
            ['key' => ['qr_code' => 1], 'sparse' => true],

            // Index textuel pour recherche plein texte
            ['key' => ['film_titre' => 'text', 'cinema_nom' => 'text', 'client_nom' => 'text']],

            // Index pour queries d'agrégation
            ['key' => ['statut' => 1, 'date_seance' => -1]],

            // Index géolocalisé si disponible (ville du cinéma)
            ['key' => ['cinema_ville' => 1]],

            // Index pour optimisation des performances temporelles
            ['key' => ['date_seance' => 1, 'statut' => 1]],

            // Index TTL pour nettoyage automatique (1 an)
            ['key' => ['created_at' => 1], 'expireAfterSeconds' => 31536000],
        ];

        foreach ($indexes as $index) {
            try {
                $this->collection->createIndex($index['key'], $index);
                Log::info('MongoDB index created', ['index' => $index['key']]);
            } catch (Exception $e) {
                Log::warning('Failed to create MongoDB index', [
                    'index' => $index['key'],
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }

    /**
     * Méthodes privées helpers
     */
    private function prepareReservationDocument(array $data): array
    {
        return [
            '_id'                 => new ObjectId,
            'reservation_id'      => $data['reservation_id'],
            'numero_reservation'  => $data['numero_reservation'] ?? null,
            'client_id'           => $data['client_id'] ?? null,
            'client_nom'          => $data['client_nom'] ?? null,
            'client_email'        => $data['client_email'] ?? null,
            'seance_id'           => $data['seance_id'],
            'film_id'             => $data['film_id'],
            'film_titre'          => $data['film_titre'] ?? null,
            'cinema_id'           => $data['cinema_id'],
            'cinema_nom'          => $data['cinema_nom'] ?? null,
            'cinema_ville'        => $data['cinema_ville'] ?? null,
            'salle_nom'           => $data['salle_nom'] ?? null,
            'salle_capacity'      => $data['salle_capacity'] ?? 100,
            'date_seance'         => isset($data['date_seance']) ? new UTCDateTime(strtotime($data['date_seance']) * 1000) : null,
            'heure_seance'        => $data['heure_seance'] ?? null,
            'places'              => $data['places'] ?? [],
            'prix_total'          => (float) ($data['prix_total'] ?? 0),
            'statut'              => $data['statut'] ?? 'en_attente',
            'numero_confirmation' => $data['numero_confirmation'] ?? null,
            'qr_code'             => $data['qr_code'] ?? null,
            'occupancy_rate'      => $this->calculateOccupancyRate($data),
            'created_at'          => new UTCDateTime,
            'updated_at'          => new UTCDateTime,
            'last_sync'           => new UTCDateTime,
            'metadata'            => [
                'source'          => 'postgresql_sync',
                'sync_version'    => '1.0',
                'processing_time' => microtime(true),
            ],
        ];
    }

    private function calculateOccupancyRate(array $data): float
    {
        $reservedSeats = count($data['places'] ?? []);
        $totalCapacity = $data['salle_capacity'] ?? 100;

        return $totalCapacity > 0 ? ($reservedSeats / $totalCapacity) * 100 : 0;
    }

    private function buildSearchQuery(array $filters): array
    {
        $query = [];

        if (isset($filters['client_id'])) {
            $query['client_id'] = $filters['client_id'];
        }

        if (isset($filters['cinema_id'])) {
            $query['cinema_id'] = $filters['cinema_id'];
        }

        if (isset($filters['film_id'])) {
            $query['film_id'] = $filters['film_id'];
        }

        if (isset($filters['statut'])) {
            if (is_array($filters['statut'])) {
                $query['statut'] = ['$in' => $filters['statut']];
            } else {
                $query['statut'] = $filters['statut'];
            }
        }

        if (isset($filters['date_from']) || isset($filters['date_to'])) {
            $dateQuery = [];
            if (isset($filters['date_from'])) {
                $dateQuery['$gte'] = new UTCDateTime(strtotime($filters['date_from']) * 1000);
            }
            if (isset($filters['date_to'])) {
                $dateQuery['$lte'] = new UTCDateTime(strtotime($filters['date_to']) * 1000);
            }
            $query['date_seance'] = $dateQuery;
        }

        if (isset($filters['search'])) {
            $query['$text'] = ['$search' => $filters['search']];
        }

        return $query;
    }

    private function buildSearchOptions(array $options): array
    {
        $mongoOptions = [];

        if (isset($options['limit'])) {
            $mongoOptions['limit'] = (int) $options['limit'];
        }

        if (isset($options['skip'])) {
            $mongoOptions['skip'] = (int) $options['skip'];
        }

        if (isset($options['sort'])) {
            $mongoOptions['sort'] = $options['sort'];
        }

        return $mongoOptions;
    }

    private function transformDocumentToArray($document): array
    {
        $array = $document->toArray();

        // Convertir les objets BSON en chaînes
        if (isset($array['_id'])) {
            $array['_id'] = $array['_id']->__toString();
        }

        // Convertir les dates
        foreach (['created_at', 'updated_at', 'date_seance', 'last_sync'] as $dateField) {
            if (isset($array[$dateField]) && $array[$dateField] instanceof UTCDateTime) {
                $array[$dateField] = $array[$dateField]->toDateTime()->format('Y-m-d H:i:s');
            }
        }

        return $array;
    }

    private function aggregate(array $pipeline): array
    {
        $startTime = microtime(true);

        try {
            $cursor  = $this->collection->aggregate($pipeline);
            $results = [];

            foreach ($cursor as $document) {
                $results[] = $this->transformDocumentToArray($document);
            }

            $this->monitorPerformance([
                'operation'       => 'aggregate',
                'collection'      => 'reservations',
                'execution_time'  => microtime(true) - $startTime,
                'pipeline_length' => count($pipeline),
                'result_count'    => count($results),
            ]);

            return $results;
        } catch (Exception $e) {
            Log::error('MongoDB aggregation failed', [
                'error'    => $e->getMessage(),
                'pipeline' => $pipeline,
            ]);
            throw $e;
        }
    }

    private function monitorPerformance(array $data): void
    {
        $this->monitor->recordBulkOperation([
            'collection'      => $data['collection'],
            'operation_count' => $data['document_count'] ?? 1,
            'deleted_count'   => 0,
            'execution_time'  => $data['execution_time'],
            'memory_usage'    => memory_get_usage(true),
            'transactional'   => false,
            'throughput'      => ($data['document_count'] ?? 1) / $data['execution_time'],
        ]);
    }
}
