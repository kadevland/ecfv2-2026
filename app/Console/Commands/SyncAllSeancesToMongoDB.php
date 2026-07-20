<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Infrastructure\Database\Models\MongoDB\SeancePublic;
use App\Infrastructure\Database\Models\Cinema\Seance as SeanceModel;

/**
 * Commande pour synchroniser toutes les séances PostgreSQL vers MongoDB
 *
 * Utilise la même logique que SyncSeanceToMongoDb listener
 */
class SyncAllSeancesToMongoDB extends Command
{
    protected $signature = 'seances:sync-to-mongodb
                          {--batch-size=100 : Nombre de séances à traiter par lot}
                          {--force : Force la synchronisation même si déjà synchronisé}
                          {--dry-run : Affichage sans modification}';

    protected $description = 'Synchronise toutes les séances PostgreSQL vers MongoDB pour le read-side CQRS';

    public function handle(): int
    {
        $batchSize = (int) $this->option('batch-size');
        $force     = $this->option('force');
        $dryRun    = $this->option('dry-run');

        $this->info('🚀 Début synchronisation séances PostgreSQL → MongoDB');

        if ($dryRun) {
            $this->warn('⚠️  MODE DRY-RUN : Aucune modification ne sera effectuée');
        }

        try {
            // Compter total séances PostgreSQL
            $totalSeances = SeanceModel::count();
            $this->info("📊 Total séances PostgreSQL : {$totalSeances}");

            // Compter séances déjà dans MongoDB
            $mongoSeances = SeancePublic::count();
            $this->info("📊 Séances déjà dans MongoDB : {$mongoSeances}");

            if (!$force && $mongoSeances >= $totalSeances) {
                $this->info('✅ Synchronisation déjà à jour ! Utilisez --force pour forcer.');

                return self::SUCCESS;
            }

            $synced = 0;
            $errors = 0;

            // Traitement par lots
            SeanceModel::with(['film', 'salle.cinema'])
                ->chunk($batchSize, function ($seances) use (&$synced, &$errors, $dryRun) {
                    foreach ($seances as $seanceModel) {
                        try {
                            if ($dryRun) {
                                $this->line("📝 Serait synchronisé : {$seanceModel->uuid}");
                            } else {
                                $this->syncSeanceData($seanceModel);
                            }
                            $synced++;
                        } catch (Exception $e) {
                            $errors++;
                            $this->error("❌ Erreur séance {$seanceModel->uuid}: {$e->getMessage()}");
                            Log::error('Sync seance failed', [
                                'seance_uuid' => $seanceModel->uuid,
                                'error'       => $e->getMessage(),
                            ]);
                        }
                    }

                    $this->info("📦 Lot traité : {$synced} synchronisées, {$errors} erreurs");
                });

            if (!$dryRun) {
                $finalCount = SeancePublic::count();
                $this->info("📊 MongoDB après sync : {$finalCount} séances");
            }

            $this->info("✅ Synchronisation terminée : {$synced} synchronisées, {$errors} erreurs");

            return $errors > 0 ? self::FAILURE : self::SUCCESS;

        } catch (Exception $e) {
            $this->error("💥 Erreur fatale : {$e->getMessage()}");
            Log::error('Sync all seances failed', ['error' => $e->getMessage()]);

            return self::FAILURE;
        }
    }

    /**
     * Synchronise une séance vers MongoDB (même logique que le listener)
     */
    private function syncSeanceData(SeanceModel $seanceModel): void
    {
        $mongoData = $this->buildMongoData($seanceModel);

        // ✅ updateOrCreate = UPSERT (update si existe, create sinon)
        SeancePublic::updateOrCreate(
            ['_id' => $seanceModel->uuid],
            $mongoData
        );
    }

    /**
     * Construction des données MongoDB (même logique que le listener)
     */
    private function buildMongoData(SeanceModel $seanceModel): array
    {
        $now = now();

        return [
            '_id'                     => $seanceModel->uuid,
            'seance_id'               => $seanceModel->uuid,
            'film_id'                 => $seanceModel->film_uuid,
            'salle_id'                => $seanceModel->salle_uuid,
            'cinema_id'               => $seanceModel->salle?->cinema_uuid ?? 'unknown',
            'date_heure_debut'        => $seanceModel->date_heure_debut,
            'date_heure_fin'          => $seanceModel->date_heure_fin,
            'version'                 => $seanceModel->version,
            'statut'                  => $seanceModel->statut,
            'placement_libre'         => $seanceModel->placement_libre,
            'tarifs'                  => $this->normalizeTarifs($seanceModel->tarification),
            'taux_tva'                => $seanceModel->taux_tva?->basisPoints ?? 2000,
            'devise'                  => $seanceModel->devise?->code ?? 'EUR',
            'options_supplementaires' => [],
            'places_totales'          => $seanceModel->salle?->capacite_totale ?? 200,
            'places_vendues'          => 0,
            'places_reservees'        => 0,
            'places_disponibles'      => $seanceModel->salle?->capacite_totale ?? 200,
            'film_titre'              => $seanceModel->film?->titre ?? 'Film inconnu',
            'cinema_nom'              => $seanceModel->salle?->cinema?->nom ?? 'Cinéma inconnu',
            'salle_nom'               => $seanceModel->salle?->nom ?? 'Salle inconnue',
            'sous_titres'             => false,
            'qualite_projection'      => 'standard',
            'created_at'              => $seanceModel->created_at ?? $now,
            'updated_at'              => $now,
        ];
    }

    /**
     * Normalisation des tarifs (même logique que le listener)
     */
    private function normalizeTarifs($tarification): array
    {
        if (is_array($tarification)) {
            return $tarification;
        }

        if (is_object($tarification) && method_exists($tarification, 'toArray')) {
            return $tarification->toArray();
        }

        if (is_string($tarification)) {
            $decoded = json_decode($tarification, true);

            return is_array($decoded) ? $decoded : [];
        }

        // Fallback : tarif par défaut
        return [
            'normal' => ['prix' => 9.50, 'tva' => 0.20],
            'reduit' => ['prix' => 7.50, 'tva' => 0.20],
        ];
    }
}
