<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

/**
 * Service de monitoring des performances des opérations MongoDB
 *
 * Collecte et analyse les métriques de performance pour optimiser
 * les opérations de nettoyage et identifier les goulots d'étranglement
 */
class MongoPerformanceMonitor
{
    private const CACHE_TTL = 3600; // 1 heure

    private array $operations = [];

    private array $performanceHistory = [];

    /**
     * Enregistre une opération bulk write
     * Complexité: O(1)
     */
    public function recordBulkOperation(array $data): void
    {
        $operation = [
            'timestamp'       => microtime(true),
            'collection'      => $data['collection'],
            'operation_count' => $data['operation_count'],
            'deleted_count'   => $data['deleted_count'] ?? 0,
            'execution_time'  => $data['execution_time'],
            'memory_usage'    => $data['memory_usage'] ?? 0,
            'transactional'   => $data['transactional'] ?? false,
            'throughput'      => $this->calculateThroughput($data),
        ];

        $this->operations[] = $operation;
        $this->updatePerformanceHistory($operation);
        $this->detectPerformanceAnomalies($operation);
    }

    /**
     * Génère un rapport de performance complet
     * Complexité: O(h) où h = nombre d'heures dans l'historique
     */
    public function generatePerformanceReport(): array
    {
        $report = [
            'summary'         => $this->generateSummaryReport(),
            'collections'     => $this->generateCollectionReports(),
            'recommendations' => $this->generatePerformanceRecommendations(),
            'anomalies'       => $this->getRecentAnomalies(),
        ];

        return $report;
    }

    /**
     * Nettoie les anciennes données de performance
     * Complexité: O(n) où n = nombre d'opérations
     */
    public function cleanupOldData(int $maxAgeHours = 24): void
    {
        $cutoffTime = microtime(true) - ($maxAgeHours * 3600);

        // Nettoyer les opérations
        $this->operations = array_filter(
            $this->operations,
            fn ($op) => $op['timestamp'] > $cutoffTime
        );

        // Nettoyer l'historique
        $cutoffHour = date('Y-m-d-H', $cutoffTime);
        foreach ($this->performanceHistory as $collection => $hours) {
            $this->performanceHistory[$collection] = array_filter(
                $hours,
                fn ($hour) => $hour > $cutoffHour,
                ARRAY_FILTER_USE_KEY
            );

            if (empty($this->performanceHistory[$collection])) {
                unset($this->performanceHistory[$collection]);
            }
        }
    }

    /**
     * Exporte les métriques pour analyse externe
     * Complexité: O(n) où n = nombre d'opérations
     */
    public function exportMetrics(): array
    {
        return [
            'operations'          => $this->operations,
            'performance_history' => $this->performanceHistory,
            'generated_at'        => microtime(true),
            'summary'             => $this->generateSummaryReport(),
        ];
    }

    /**
     * Calcule le débit de traitement
     * Complexité: O(1)
     */
    private function calculateThroughput(array $data): float
    {
        $executionTime = $data['execution_time'] ?? 0.001; // Éviter division par zéro

        return ($data['operation_count'] ?? 0) / $executionTime;
    }

    /**
     * Met à jour l'historique de performance
     * Complexité: O(1)
     */
    private function updatePerformanceHistory(array $operation): void
    {
        $collection = $operation['collection'];
        $hour       = date('Y-m-d-H');

        if (!isset($this->performanceHistory[$collection])) {
            $this->performanceHistory[$collection] = [];
        }

        if (!isset($this->performanceHistory[$collection][$hour])) {
            $this->performanceHistory[$collection][$hour] = [
                'operations'     => 0,
                'total_time'     => 0,
                'total_memory'   => 0,
                'total_deleted'  => 0,
                'avg_throughput' => 0,
                'max_throughput' => 0,
                'min_throughput' => PHP_FLOAT_MAX,
            ];
        }

        $stats = &$this->performanceHistory[$collection][$hour];
        $stats['operations']++;
        $stats['total_time'] += $operation['execution_time'];
        $stats['total_memory'] = max($stats['total_memory'], $operation['memory_usage']);
        $stats['total_deleted'] += $operation['deleted_count'];
        $stats['avg_throughput'] = $stats['operations'] / $stats['total_time'];
        $stats['max_throughput'] = max($stats['max_throughput'], $operation['throughput']);
        $stats['min_throughput'] = min($stats['min_throughput'], $operation['throughput']);

        // Mettre en cache les statistiques
        $this->cachePerformanceStats($collection, $hour, $stats);
    }

    /**
     * Détecte les anomalies de performance
     * Complexité: O(1)
     */
    private function detectPerformanceAnomalies(array $operation): void
    {
        $collection = $operation['collection'];

        // Récupérer les performances moyennes historiques
        $historicalAvg = $this->getHistoricalAverageThroughput($collection);

        if ($historicalAvg > 0) {
            $performanceRatio = $operation['throughput'] / $historicalAvg;

            // Alerte si la performance est significativement inférieure
            if ($performanceRatio < 0.5) {
                $this->alertPerformanceDegradation($operation, $historicalAvg);
            }

            // Alerte si la mémoire usage est trop élevé
            if ($operation['memory_usage'] > 512 * 1024 * 1024) { // 512MB
                $this->alertHighMemoryUsage($operation);
            }

            // Alerte si le temps d'exécution est anormal
            if ($operation['execution_time'] > 30) { // 30 secondes
                $this->alertSlowExecution($operation);
            }
        }
    }

    /**
     * Récupère le débit moyen historique
     * Complexité: O(1) avec cache
     */
    private function getHistoricalAverageThroughput(string $collection): float
    {
        $cacheKey = "mongo_perf_avg_{$collection}";

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($collection) {
            if (!isset($this->performanceHistory[$collection])) {
                return 0;
            }

            $totalThroughput = 0;
            $totalOperations = 0;

            foreach ($this->performanceHistory[$collection] as $hourStats) {
                $totalThroughput += $hourStats['avg_throughput'];
                $totalOperations++;
            }

            return $totalOperations > 0 ? $totalThroughput / $totalOperations : 0;
        });
    }

    /**
     * Alerte de dégradation de performance
     */
    private function alertPerformanceDegradation(array $operation, float $historicalAvg): void
    {
        $message = sprintf(
            'Dégradation de performance détectée pour %s: %.2f ops/s vs %.2f ops/s (historique)',
            $operation['collection'],
            $operation['throughput'],
            $historicalAvg
        );

        Log::warning($message, [
            'operation'          => $operation,
            'historical_average' => $historicalAvg,
        ]);
    }

    /**
     * Alerte d'utilisation mémoire élevée
     */
    private function alertHighMemoryUsage(array $operation): void
    {
        $memoryMB = $operation['memory_usage'] / 1024 / 1024;

        Log::warning(sprintf(
            'Utilisation mémoire élevée pour %s: %.1f MB',
            $operation['collection'],
            $memoryMB
        ), ['operation' => $operation]);
    }

    /**
     * Alerte d'exécution lente
     */
    private function alertSlowExecution(array $operation): void
    {
        Log::warning(sprintf(
            'Exécution lente détectée pour %s: %.2f secondes',
            $operation['collection'],
            $operation['execution_time']
        ), ['operation' => $operation]);
    }

    /**
     * Met en cache les statistiques de performance
     * Complexité: O(1)
     */
    private function cachePerformanceStats(string $collection, string $hour, array $stats): void
    {
        $cacheKey = "mongo_perf_stats_{$collection}_{$hour}";
        Cache::put($cacheKey, $stats, self::CACHE_TTL);
    }

    /**
     * Génère le rapport résumé
     * Complexité: O(1)
     */
    private function generateSummaryReport(): array
    {
        $totalOperations = count($this->operations);
        $totalDeleted    = array_sum(array_column($this->operations, 'deleted_count'));
        $totalTime       = array_sum(array_column($this->operations, 'execution_time'));
        $avgThroughput   = $totalOperations / max($totalTime, 0.001);

        return [
            'total_operations'          => $totalOperations,
            'total_documents_deleted'   => $totalDeleted,
            'total_execution_time'      => $totalTime,
            'average_throughput'        => $avgThroughput,
            'peak_memory_usage'         => max(array_column($this->operations, 'memory_usage')),
            'operations_per_collection' => $this->countOperationsPerCollection(),
        ];
    }

    /**
     * Compte les opérations par collection
     * Complexité: O(n) où n = nombre d'opérations
     */
    private function countOperationsPerCollection(): array
    {
        $counts = [];
        foreach ($this->operations as $operation) {
            $collection          = $operation['collection'];
            $counts[$collection] = ($counts[$collection] ?? 0) + 1;
        }

        return $counts;
    }

    /**
     * Génère les rapports par collection
     * Complexité: O(c*h) où c = collections, h = heures par collection
     */
    private function generateCollectionReports(): array
    {
        $reports = [];

        foreach ($this->performanceHistory as $collection => $hours) {
            $collectionReport = [
                'collection'        => $collection,
                'total_operations'  => 0,
                'total_deleted'     => 0,
                'avg_throughput'    => 0,
                'max_throughput'    => 0,
                'min_throughput'    => PHP_FLOAT_MAX,
                'performance_trend' => $this->calculatePerformanceTrend($hours),
                'efficiency_score'  => $this->calculateEfficiencyScore($collection),
            ];

            foreach ($hours as $hourStats) {
                $collectionReport['total_operations'] += $hourStats['operations'];
                $collectionReport['total_deleted'] += $hourStats['total_deleted'];
                $collectionReport['avg_throughput'] += $hourStats['avg_throughput'];
                $collectionReport['max_throughput'] = max($collectionReport['max_throughput'], $hourStats['max_throughput']);
                $collectionReport['min_throughput'] = min($collectionReport['min_throughput'], $hourStats['min_throughput']);
            }

            if (count($hours) > 0) {
                $collectionReport['avg_throughput'] /= count($hours);
            }

            $reports[$collection] = $collectionReport;
        }

        return $reports;
    }

    /**
     * Calcule la tendance de performance
     * Complexité: O(h) où h = nombre d'heures
     */
    private function calculatePerformanceTrend(array $hours): string
    {
        $throughputs = [];
        foreach ($hours as $hourStats) {
            $throughputs[] = $hourStats['avg_throughput'];
        }

        if (count($throughputs) < 2) {
            return 'stable';
        }

        $firstHalf  = array_slice($throughputs, 0, count($throughputs) / 2);
        $secondHalf = array_slice($throughputs, count($throughputs) / 2);

        $firstAvg  = array_sum($firstHalf) / count($firstHalf);
        $secondAvg = array_sum($secondHalf) / count($secondHalf);

        $ratio = $secondAvg / $firstAvg;

        if ($ratio > 1.1) {
            return 'improving';
        } elseif ($ratio < 0.9) {
            return 'degrading';
        } else {
            return 'stable';
        }
    }

    /**
     * Calcule un score d'efficacité pour une collection
     * Complexité: O(1)
     */
    private function calculateEfficiencyScore(string $collection): float
    {
        $historicalAvg = $this->getHistoricalAverageThroughput($collection);

        if ($historicalAvg === 0) {
            return 0;
        }

        // Facteurs qui influencent l'efficacité
        $recentPerformance = $this->getRecentAverageThroughput($collection);
        $memoryEfficiency  = $this->calculateMemoryEfficiency($collection);
        $consistency       = $this->calculatePerformanceConsistency($collection);

        // Score pondéré
        $score = (
            ($recentPerformance / $historicalAvg) * 0.4 +
            $memoryEfficiency * 0.3 +
            $consistency * 0.3
        ) * 100;

        return min(100, max(0, $score));
    }

    /**
     * Calcule le débit moyen récent
     */
    private function getRecentAverageThroughput(string $collection): float
    {
        $recentOperations = array_filter(
            $this->operations,
            fn ($op) => $op['collection'] === $collection &&
                     (microtime(true) - $op['timestamp']) < 3600 // Dernière heure
        );

        if (empty($recentOperations)) {
            return 0;
        }

        return array_sum(array_column($recentOperations, 'throughput')) / count($recentOperations);
    }

    /**
     * Calcule l'efficacité mémoire
     */
    private function calculateMemoryEfficiency(string $collection): float
    {
        $collectionOps = array_filter(
            $this->operations,
            fn ($op) => $op['collection'] === $collection
        );

        if (empty($collectionOps)) {
            return 1;
        }

        $avgMemory           = array_sum(array_column($collectionOps, 'memory_usage')) / count($collectionOps);
        $maxAcceptableMemory = 256 * 1024 * 1024; // 256MB

        return min(1, $maxAcceptableMemory / max($avgMemory, 1));
    }

    /**
     * Calcule la cohérence de performance
     */
    private function calculatePerformanceConsistency(string $collection): float
    {
        $collectionOps = array_filter(
            $this->operations,
            fn ($op) => $op['collection'] === $collection
        );

        if (count($collectionOps) < 2) {
            return 1;
        }

        $throughputs = array_column($collectionOps, 'throughput');
        $avg         = array_sum($throughputs) / count($throughputs);
        $variance    = array_sum(array_map(fn ($t) => pow($t - $avg, 2), $throughputs)) / count($throughputs);
        $stdDev      = sqrt($variance);

        // Plus l'écart-type est faible, plus la performance est cohérente
        return max(0, 1 - ($stdDev / max($avg, 1)));
    }

    /**
     * Génère des recommandations d'optimisation
     * Complexité: O(c) où c = nombre de collections
     */
    private function generatePerformanceRecommendations(): array
    {
        $recommendations = [];

        foreach ($this->performanceHistory as $collection => $hours) {
            $efficiencyScore = $this->calculateEfficiencyScore($collection);
            $trend           = $this->calculatePerformanceTrend($hours);

            if ($efficiencyScore < 50) {
                $recommendations[] = [
                    'collection' => $collection,
                    'priority'   => 'high',
                    'type'       => 'performance',
                    'message'    => "Faible efficacité de performance ({$efficiencyScore}%)",
                    'suggestion' => 'Considérer l\'optimisation des indexes ou la réduction de la taille des lots',
                ];
            }

            if ($trend === 'degrading') {
                $recommendations[] = [
                    'collection' => $collection,
                    'priority'   => 'medium',
                    'type'       => 'trend',
                    'message'    => 'Tendance de performance en dégradation',
                    'suggestion' => 'Surveiller l\'utilisation des ressources et considérer le scaling',
                ];
            }
        }

        return $recommendations;
    }

    /**
     * Récupère les anomalies récentes
     * Complexité: O(1) avec cache
     */
    private function getRecentAnomalies(): array
    {
        return Cache::get('mongo_perf_anomalies', []);
    }
}
