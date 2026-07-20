<?php

declare(strict_types=1);

namespace App\Services;

use MongoDB\Collection;
use MongoDB\Driver\Exception\RuntimeException;

/**
 * Service d'analyse et d'optimisation des indexes MongoDB
 *
 * Permet d'optimiser les requêtes de nettoyage en utilisant
 * stratégiquement les indexes disponibles
 */
class MongoIndexAnalyzer
{
    /**
     * Analyse les indexes disponibles pour une collection
     * Complexité: O(m) où m = nombre d'indexes
     */
    public function analyzeIndexes(Collection $collection): array
    {
        try {
            $indexes  = $collection->listIndexes()->toArray();
            $analysis = [
                'indexes'             => [],
                'recommendations'     => [],
                'optimal_for_cleanup' => null,
            ];

            foreach ($indexes as $index) {
                $indexInfo = [
                    'name'               => $index['name'],
                    'keys'               => $index['key'],
                    'unique'             => $index['unique'] ?? false,
                    'sparse'             => $index['sparse'] ?? false,
                    'background'         => $index['background'] ?? false,
                    'useful_for_cleanup' => $this->isIndexUsefulForCleanup($index),
                ];

                $analysis['indexes'][] = $indexInfo;

                if ($indexInfo['useful_for_cleanup']) {
                    $analysis['optimal_for_cleanup'] = $indexInfo;
                }
            }

            $analysis['recommendations'] = $this->generateIndexRecommendations($analysis['indexes']);

            return $analysis;

        } catch (RuntimeException $e) {
            throw new \RuntimeException("Impossible d'analyser les indexes: " . $e->getMessage());
        }
    }

    /**
     * Détermine l'index optimal pour un filtre donné
     * Complexité: O(m) où m = nombre d'indexes
     */
    public function getOptimalIndexForFilter(Collection $collection, array $filter): ?array
    {
        try {
            $indexes       = $collection->listIndexes()->toArray();
            $scoredIndexes = [];

            foreach ($indexes as $index) {
                $score = $this->calculateIndexScore($index, $filter);
                if ($score > 0) {
                    $scoredIndexes[] = [
                        'index'            => $index,
                        'score'            => $score,
                        'optimized_filter' => $this->optimizeFilterForIndex($filter, $index),
                    ];
                }
            }

            // Trier par score décroissant
            usort($scoredIndexes, fn ($a, $b) => $b['score'] - $a['score']);

            return $scoredIndexes[0] ?? null;

        } catch (RuntimeException $e) {
            return null;
        }
    }

    /**
     * Analyse l'utilisation des indexes avec $indexStats
     * Complexité: O(m) où m = nombre d'indexes
     */
    public function analyzeIndexUsage(Collection $collection): array
    {
        try {
            $stats = $collection->aggregate([
                ['$indexStats' => []],
            ])->toArray();

            $usage = [];
            foreach ($stats as $stat) {
                $usage[] = [
                    'name'     => $stat['name'],
                    'accesses' => $stat['accesses'] ?? ['ops' => 0, 'since' => null],
                    'size'     => $stat['size'] ?? 0,
                ];
            }

            // Identifier les indexes non utilisés
            $unusedIndexes = array_filter($usage, fn ($stat) => ($stat['accesses']['ops'] ?? 0) === 0
            );

            return [
                'usage'          => $usage,
                'unused_indexes' => $unusedIndexes,
                'total_size'     => array_sum(array_column($usage, 'size')),
            ];

        } catch (RuntimeException $e) {
            throw new \RuntimeException("Impossible d'analyser l'utilisation des indexes: " . $e->getMessage());
        }
    }

    /**
     * Suggère la création d'indexes composés pour le nettoyage
     * Complexité: O(1)
     */
    public function suggestCompositeIndexes(array $commonFilters): array
    {
        $suggestions = [];

        // Analyser les combinaisons fréquentes de filtres
        $combinations = [];
        foreach ($commonFilters as $filter) {
            $fields = array_keys($filter);
            sort($fields);
            $key                = implode(',', $fields);
            $combinations[$key] = ($combinations[$key] ?? 0) + 1;
        }

        // Suggérer des indexes composés pour les combinaisons fréquentes
        foreach ($combinations as $combination => $frequency) {
            if ($frequency >= 3) { // Si utilisé au moins 3 fois
                $fields = explode(',', $combination);
                if (count($fields) > 1) {
                    $suggestions[] = [
                        'fields'     => $fields,
                        'frequency'  => $frequency,
                        'definition' => $this->buildCompositeIndexDefinition($fields),
                    ];
                }
            }
        }

        return $suggestions;
    }

    /**
     * Vérifie si un index est utile pour les opérations de nettoyage
     * Complexité: O(1)
     */
    private function isIndexUsefulForCleanup(array $index): bool
    {
        $keys = array_keys($index['key']);

        // Index utiles pour le nettoyage basé sur le temps
        $timeFields = ['created_at', 'updated_at', 'timestamp', 'date', 'expiry'];

        // Index utiles pour le nettoyage basé sur le statut
        $statusFields = ['status', 'active', 'deleted', 'archived'];

        foreach ($keys as $key) {
            if (in_array($key, $timeFields) || in_array($key, $statusFields)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Calcule le score de pertinence d'un index pour un filtre
     * Complexité: O(k) où k = nombre de champs dans l'index
     */
    private function calculateIndexScore(array $index, array $filter): int
    {
        $score      = 0;
        $indexKeys  = array_keys($index['key']);
        $filterKeys = array_keys($filter);

        // Score de base pour chaque champ correspondant
        foreach ($indexKeys as $indexKey) {
            foreach ($filterKeys as $filterKey) {
                if ($indexKey === $filterKey) {
                    $score += 10;
                    // Bonus pour les index composés avec le bon ordre
                    $position       = array_search($indexKey, $indexKeys);
                    $filterPosition = array_search($filterKey, $filterKeys);
                    if ($position === $filterPosition) {
                        $score += 5;
                    }
                }
            }
        }

        // Bonus pour les indexes uniques
        if ($index['unique'] ?? false) {
            $score += 3;
        }

        // Bonus pour les indexes couvrants
        if ($this->isCoveringIndex($index, $filter)) {
            $score += 8;
        }

        return $score;
    }

    /**
     * Vérifie si un index peut couvrir la requête
     * Complexité: O(k) où k = nombre de champs dans l'index
     */
    private function isCoveringIndex(array $index, array $filter): bool
    {
        $indexKeys  = array_keys($index['key']);
        $filterKeys = array_keys($filter);

        // Vérifie si tous les champs du filtre sont dans l'index
        foreach ($filterKeys as $filterKey) {
            if (!in_array($filterKey, $indexKeys)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Optimise un filtre pour utiliser un index spécifique
     * Complexité: O(k) où k = nombre de champs dans le filtre
     */
    private function optimizeFilterForIndex(array $filter, array $index): array
    {
        $optimized = $filter;
        $indexKeys = array_keys($index['key']);

        // Réorganiser le filtre pour correspondre à l'ordre des index
        $orderedFilter = [];
        foreach ($indexKeys as $key) {
            if (isset($optimized[$key])) {
                $orderedFilter[$key] = $optimized[$key];
            }
        }

        // Ajouter les champs non indexés à la fin
        foreach ($optimized as $key => $value) {
            if (!isset($orderedFilter[$key])) {
                $orderedFilter[$key] = $value;
            }
        }

        return $orderedFilter;
    }

    /**
     * Génère des recommandations d'optimisation d'indexes
     * Complexité: O(m) où m = nombre d'indexes
     */
    private function generateIndexRecommendations(array $indexes): array
    {
        $recommendations = [];
        $indexNames      = array_column($indexes, 'name');
        $hasTimeIndex    = false;
        $hasStatusIndex  = false;

        foreach ($indexes as $index) {
            $keys = array_keys($index['keys']);

            if (in_array('created_at', $keys) || in_array('updated_at', $keys)) {
                $hasTimeIndex = true;
            }

            if (in_array('status', $keys) || in_array('active', $keys)) {
                $hasStatusIndex = true;
            }
        }

        if (!$hasTimeIndex) {
            $recommendations[] = [
                'type'        => 'create_index',
                'priority'    => 'high',
                'description' => 'Créer un index sur created_at pour les opérations de nettoyage basées sur le temps',
                'suggestion'  => 'db.collection.createIndex({created_at: 1}, {background: true})',
            ];
        }

        if (!$hasStatusIndex) {
            $recommendations[] = [
                'type'        => 'create_index',
                'priority'    => 'medium',
                'description' => 'Créer un index sur status pour filtrer efficacement les documents à nettoyer',
                'suggestion'  => 'db.collection.createIndex({status: 1}, {background: true})',
            ];
        }

        // Vérifier les indexes dupliqués ou redondants
        if (count($indexes) > 10) {
            $recommendations[] = [
                'type'        => 'cleanup',
                'priority'    => 'medium',
                'description' => 'Considérer la suppression des indexes non utilisés',
                'suggestion'  => 'Analyser l\'utilisation des indexes avec $indexStats',
            ];
        }

        return $recommendations;
    }

    /**
     * Construit la définition d'un index composé
     * Complexité: O(k) où k = nombre de champs
     */
    private function buildCompositeIndexDefinition(array $fields): string
    {
        $definition = '{';
        foreach ($fields as $field) {
            $definition .= "{$field}: 1, ";
        }
        $definition = rtrim($definition, ', ') . '}';

        return "db.collection.createIndex({$definition}, {background: true})";
    }
}
