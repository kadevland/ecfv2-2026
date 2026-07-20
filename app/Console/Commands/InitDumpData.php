<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

/**
 * Commande master pour initialiser toutes les données de test
 *
 * Exécute dans l'ordre : cinemas → salles → films → séances → comptes clients
 */
class InitDumpData extends Command
{
    protected $signature = 'init:dump-data
                          {--skip-cinema : Skip cinema import}
                          {--skip-salle : Skip salle import}
                          {--skip-film : Skip film import}
                          {--skip-seance : Skip seance generation}
                          {--skip-compte : Skip compte import}
                          {--force : Force reimport even if data exists}';

                          //{--skip-sync : Skip MongoDB sync}

    protected $description = 'Initialise toutes les données : cinemas, salles, films, séances, comptes + sync MongoDB';

    private array $stats = [];

    public function handle(): int
    {
        $this->info('🚀 Initialisation complète des données Cinéphoria');
        $this->info('📋 Ordre : Cinémas → Salles → Films → Séances → Comptes → Sync MongoDB');

        $startTime = microtime(true);

        try {
            // 1. Import Cinémas
            if (!$this->option('skip-cinema')) {
                $this->executeStep('Cinémas', 'cinemas:import');
                $this->executeStep('Sync MongoDB', 'events:trigger-cinema', ['--type' => 'cinemas', '--limit' => 100]);
            }

            // 2. Import Salles
            if (!$this->option('skip-salle')) {
                $this->executeStep('Salles', 'salles:import');
                $this->executeStep('Sync MongoDB', 'events:trigger-cinema', ['--type' => 'salles', '--limit' => 100]);
            }

            // 3. Import Films
            if (!$this->option('skip-film')) {
                $this->executeStep('Films', 'films:import');
                $this->executeStep('Sync MongoDB', 'events:trigger-cinema', ['--type' => 'films', '--limit' => 100]);
            }

             // 5. Import Comptes
            if (!$this->option('skip-compte')) {
                $this->executeStep('Comptes', 'comptes:import');
            }


            // 4. Génération Séances
            if (!$this->option('skip-seance')) {
                //$this->executeStep('Séances', 'generate:seances-2026');
                //$this->executeStep('Séances', 'seances:generate-programming');
                //$this->executeStep('Séances', 'seances:generate-2026');
                $this->executeStep('Séances', 'seances:generate');
            }



            // 6. Sync MongoDB
            /*if (!$this->option('skip-sync')) {
                //$this->executeStep('Sync MongoDB', 'seances:sync-to-mongodb', ['--force' => true]);
                $this->executeStep('Sync MongoDB', 'events:trigger-cinema', ['--type' => 'all', '--limit' => 500]);
            }*/

            $duration = round(microtime(true) - $startTime, 2);

            $this->displaySummary($duration);

            return self::SUCCESS;

        } catch (Exception $e) {
            $this->error("💥 Erreur fatale : {$e->getMessage()}");
            Log::error('Init dump data failed', ['error' => $e->getMessage()]);

            return self::FAILURE;
        }
    }

    /**
     * Exécute une étape avec gestion d'erreur et timing
     */
    private function executeStep(string $stepName, string $command, array $options = []): void
    {
        $this->info("📦 Étape {$stepName}...");

        $stepStart = microtime(true);

        try {
            // Ajouter --force si option globale activée
            if ($this->option('force') && !isset($options['--force'])) {
                $options['--force'] = true;
            }

            // Exécuter la commande
            $exitCode = Artisan::call($command, $options);

            $stepDuration = round(microtime(true) - $stepStart, 2);

            if ($exitCode === 0) {
                $this->info("✅ {$stepName} terminé en {$stepDuration}s");
                $this->stats[$stepName] = [
                    'status'   => 'success',
                    'duration' => $stepDuration,
                    'output'   => Artisan::output(),
                ];
            } else {
                throw new Exception("Command {$command} failed with exit code {$exitCode}");
            }

        } catch (Exception $e) {
            $stepDuration = round(microtime(true) - $stepStart, 2);
            $this->error("❌ {$stepName} échoué après {$stepDuration}s : {$e->getMessage()}");

            $this->stats[$stepName] = [
                'status'   => 'failed',
                'duration' => $stepDuration,
                'error'    => $e->getMessage(),
            ];

            throw $e; // Re-lancer pour arrêter le processus
        }
    }

    /**
     * Affiche un résumé final
     */
    private function displaySummary(float $totalDuration): void
    {
        $this->newLine();
        $this->info('📊 RÉSUMÉ FINAL');
        $this->info("⏱️  Durée totale : {$totalDuration}s");

        $successCount = 0;
        $failedCount  = 0;

        foreach ($this->stats as $stepName => $stat) {
            $status   = $stat['status'] === 'success' ? '✅' : '❌';
            $duration = $stat['duration'];

            $this->line("{$status} {$stepName} ({$duration}s)");

            if ($stat['status'] === 'success') {
                $successCount++;
            } else {
                $failedCount++;
            }
        }

        $this->newLine();
        if ($failedCount === 0) {
            $this->info("🎯 SUCCÈS COMPLET ! {$successCount} étapes réussies");
            $this->info('🚀 Base de données Cinéphoria prête !');
        } else {
            $this->warn("⚠️  {$successCount} réussites, {$failedCount} échecs");
        }

        // Afficher les compteurs finaux
        $this->displayFinalStats();
    }

    /**
     * Affiche les statistiques finales de la base
     */
    private function displayFinalStats(): void
    {
        try {
            $this->newLine();
            $this->info('📈 STATISTIQUES FINALES');

            // Compter PostgreSQL
            $cinemas = DB::table('cinema.cinemas')->count();
            $salles  = DB::table('cinema.salles')->count();
            $films   = DB::table('cinema.films')->count();
            $seances = DB::table('cinema.seances')->count();
            $users   = DB::table('auth.users')->count();

            $this->line("🏢 Cinémas : {$cinemas}");
            $this->line("🎭 Salles : {$salles}");
            $this->line("🎬 Films : {$films}");
            $this->line("🎟️  Séances PostgreSQL : {$seances}");
            $this->line("👥 Utilisateurs : {$users}");

            // Compter MongoDB si accessible
            try {
                $mongoSeances = \App\Infrastructure\Database\Models\MongoDB\SeancePublic::count();
                $this->line("📊 Séances MongoDB : {$mongoSeances}");

                if ($mongoSeances === $seances && $seances > 0) {
                    $this->info('✅ Sync CQRS parfaite !');
                } elseif ($seances > 0) {
                    $this->warn('⚠️  Sync CQRS incomplète');
                }
            } catch (Exception $e) {
                $this->warn('⚠️  MongoDB inaccessible');
            }

        } catch (Exception $e) {
            $this->warn("⚠️  Impossible d'afficher les stats finales : {$e->getMessage()}");
        }
    }
}
