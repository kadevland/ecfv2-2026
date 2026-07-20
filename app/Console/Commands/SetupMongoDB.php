<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Exception;
use MongoDB\Collection;
use Illuminate\Console\Command;
use MongoDB\Laravel\Connection;

/**
 * Commande pour initialiser MongoDB avec collections et index optimisés
 */
class SetupMongoDB extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'mongodb:setup {--force : Force la recréation des collections}';

    /**
     * The console command description.
     */
    protected $description = 'Initialise MongoDB avec les collections et index optimisés pour CQRS read-side';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('🚀 Initialisation de MongoDB pour Cinéphoria...');

        try {
            /** @var Connection $mongodb */
            $mongodb  = app('db')->connection('mongodb');
            $database = $mongodb->getMongoDB();

            $force = $this->option('force');

            if ($force) {
                $this->warn('⚠️  Mode --force activé : suppression des collections existantes...');
                $this->dropCollectionsIfExist($database);
            }

            // Créer les collections avec validation de schéma
            $this->createCollectionsWithValidation($database);

            // Créer les index optimisés
            $this->createOptimizedIndexes($database);

            $this->info('✅ MongoDB initialisé avec succès !');
            $this->newLine();
            $this->info('Collections créées :');
            $this->line('  • film_reviews (avis par film)');
            $this->line('  • cinemas_public (infos publiques cinémas)');
            $this->line('  • seances_live (séances temps réel)');
            $this->line('  • films_catalogue (catalogue public)');
            $this->line('  • stats_realtime (stats dashboard)');
            $this->line('  • stats_daily (stats quotidiennes)');
            $this->line('  • stats_weekly (stats hebdomadaires)');
            $this->line('  • stats_monthly (stats mensuelles)');
            $this->line('  • stats_yearly (stats annuelles)');

            return self::SUCCESS;

        } catch (Exception $e) {
            $this->error('❌ Erreur lors de l\'initialisation MongoDB : ' . $e->getMessage());

            return self::FAILURE;
        }
    }

    /**
     * Supprime les collections existantes si --force
     *
     * @param mixed $database
     */
    private function dropCollectionsIfExist($database): void
    {
        $collections = [
            'film_reviews', 'cinemas_public', 'seances_live', 'films_catalogue',
            'stats_realtime', 'stats_daily', 'stats_weekly', 'stats_monthly', 'stats_yearly',
        ];

        foreach ($collections as $collection) {
            try {
                $database->dropCollection($collection);
                $this->line("  Supprimé : {$collection}");
            } catch (Exception $e) {
                // Collection n'existe pas, continuer
            }
        }
    }

    /**
     * Crée les collections avec validation de schéma MongoDB
     *
     * @param mixed $database
     */
    private function createCollectionsWithValidation($database): void
    {
        $this->info('📝 Création des collections avec validation...');

        // Collection film_reviews avec validation
        $database->createCollection('film_reviews', [
            'validator' => [
                '$jsonSchema' => [
                    'bsonType'   => 'object',
                    'required'   => ['film_id', 'titre_film', 'total_reviews', 'note_moyenne'],
                    'properties' => [
                        'film_id'            => ['bsonType' => 'string'],
                        'titre_film'         => ['bsonType' => 'string'],
                        'total_reviews'      => ['bsonType' => 'int', 'minimum' => 0],
                        'note_moyenne'       => ['bsonType' => 'double', 'minimum' => 0, 'maximum' => 5],
                        'distribution_notes' => ['bsonType' => 'object'],
                        'avis'               => ['bsonType' => 'array'],
                    ],
                ],
            ],
        ]);

        // Collection cinemas_public avec validation
        $database->createCollection('cinemas_public', [
            'validator' => [
                '$jsonSchema' => [
                    'bsonType'   => 'object',
                    'required'   => ['cinema_id', 'nom', 'ville', 'statut'],
                    'properties' => [
                        'cinema_id'     => ['bsonType' => 'string'],
                        'nom'           => ['bsonType' => 'string'],
                        'ville'         => ['bsonType' => 'string'],
                        'statut'        => ['enum' => ['actif', 'inactif', 'maintenance']],
                        'nombre_salles' => ['bsonType' => 'int', 'minimum' => 0],
                    ],
                ],
            ],
        ]);

        // Collection seances_live avec validation
        $database->createCollection('seances_live', [
            'validator' => [
                '$jsonSchema' => [
                    'bsonType'   => 'object',
                    'required'   => ['seance_id', 'film_id', 'cinema_id', 'date_heure_debut'],
                    'properties' => [
                        'seance_id'          => ['bsonType' => 'string'],
                        'film_id'            => ['bsonType' => 'string'],
                        'cinema_id'          => ['bsonType' => 'string'],
                        'places_totales'     => ['bsonType' => 'int', 'minimum' => 0],
                        'places_disponibles' => ['bsonType' => 'int', 'minimum' => 0],
                        'statut'             => ['enum' => ['active', 'cancelled', 'completed']],
                    ],
                ],
            ],
        ]);

        // Collections stats sans validation stricte (flexibilité)
        $database->createCollection('films_catalogue');
        $database->createCollection('stats_realtime');
        $database->createCollection('stats_daily');
        $database->createCollection('stats_weekly');
        $database->createCollection('stats_monthly');
        $database->createCollection('stats_yearly');
    }

    /**
     * Crée les index optimisés pour chaque collection
     *
     * @param mixed $database
     */
    private function createOptimizedIndexes($database): void
    {
        $this->info('🔍 Création des index optimisés...');

        // Index film_reviews
        $this->createIndexes($database->selectCollection('film_reviews'), [
            ['film_id' => 1],
            ['film_id'            => 1, 'avis.statut_moderation' => 1],
            ['note_moyenne'       => -1],
            ['avis.review_id'     => 1],
            ['avis.date_creation' => -1],
        ]);

        // Index cinemas_public
        $this->createIndexes($database->selectCollection('cinemas_public'), [
            ['cinema_id' => 1],
            ['ville'  => 1],
            ['statut' => 1],
            ['nom'    => 'text'],
        ]);

        // Index seances_live
        $this->createIndexes($database->selectCollection('seances_live'), [
            ['seance_id' => 1],
            ['film_id'            => 1, 'date_heure_debut' => 1],
            ['cinema_id'          => 1, 'date_heure_debut' => 1],
            ['places_disponibles' => 1, 'statut' => 1],
            ['date_heure_debut'   => 1],
            ['statut'             => 1],
        ]);

        // Index films_catalogue
        $this->createIndexes($database->selectCollection('films_catalogue'), [
            ['film_id' => 1],
            ['titre'            => 'text', 'description' => 'text'],
            ['genre'            => 1],
            ['note_moyenne'     => -1],
            ['date_sortie'      => -1],
            ['statut_diffusion' => 1],
        ]);

        // Index stats avec TTL pour stats_realtime
        $database->selectCollection('stats_realtime')->createIndex(
            ['timestamp' => 1],
            ['expireAfterSeconds' => 604800] // 7 jours
        );

        $this->createIndexes($database->selectCollection('stats_daily'), [
            ['date' => 1],
            ['date' => -1],
        ]);

        $this->createIndexes($database->selectCollection('stats_weekly'), [
            ['semaine' => 1, 'annee' => 1],
            ['annee' => -1, 'semaine' => -1],
        ]);

        $this->createIndexes($database->selectCollection('stats_monthly'), [
            ['mois' => 1, 'annee' => 1],
            ['annee' => -1, 'mois' => -1],
        ]);

        $this->createIndexes($database->selectCollection('stats_yearly'), [
            ['annee' => 1],
            ['annee' => -1],
        ]);
    }

    /**
     * Helper pour créer plusieurs index sur une collection
     */
    /**
     * @param array<array<string, mixed>> $indexes
     */
    private function createIndexes(Collection $collection, array $indexes): void
    {
        foreach ($indexes as $index) {
            try {
                $collection->createIndex($index);
            } catch (Exception $e) {
                $this->warn('Index déjà existant ou erreur : ' . $e->getMessage());
            }
        }
    }
}
