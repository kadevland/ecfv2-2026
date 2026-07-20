<?php

declare(strict_types=1);

namespace App\Application\Cinema\Listeners;

use Exception;
use Illuminate\Support\Facades\Log;
use App\Domain\Cinema\Events\SeanceCreated;
use App\Domain\Cinema\Events\SeanceDeleted;
use App\Domain\Cinema\Events\SeanceUpdated;
use App\Domain\Cinema\Events\SeanceStatusChanged;
use App\Infrastructure\Database\Models\MongoDB\SeancePublic;
use App\Infrastructure\Database\Models\Cinema\Seance as SeanceModel;

/**
 * Synchronise les changements de séance vers MongoDB pour le read-side CQRS
 */
final class SyncSeanceToMongoDb
{
    /**
     * Handle seance creation event
     */
    public function handleSeanceCreated(SeanceCreated $event): void
    {
        $seanceUuid = $event->getSeanceUuid();

        Log::info('🎬 [CQRS] SeanceCreated event received', [
            'seance_uuid' => $seanceUuid,
            'timestamp'   => now()->toISOString(),
        ]);

        try {
            // Charger depuis PostgreSQL
            $seanceModel = SeanceModel::where('uuid', $seanceUuid)->first();
            if (!$seanceModel) {
                Log::error('❌ [CQRS] Seance not found in PostgreSQL', [
                    'seance_uuid' => $seanceUuid,
                    'searched_in' => 'cinema.seances',
                ]);

                return;
            }

            Log::info('✅ [CQRS] Seance found in PostgreSQL', [
                'seance_uuid' => $seanceUuid,
                'film_uuid'   => $seanceModel->film_uuid,
                'salle_uuid'  => $seanceModel->salle_uuid,
            ]);

            // Charger les relations pour récupérer les données
            $seanceModel->load(['film', 'salle.cinema']);

            Log::info('🔄 [CQRS] Starting MongoDB sync', [
                'seance_uuid' => $seanceUuid,
                'operation'   => 'create',
            ]);

            // ✅ FIX : Utiliser la logique commune pour éviter les duplicates
            $this->syncSeanceData($seanceModel, 'created');

            Log::info('🚀 [CQRS] Seance synced to MongoDB successfully', [
                'seance_uuid'        => $seanceUuid,
                'event'              => 'created',
                'mongodb_collection' => 'seance_publics',
            ]);

        } catch (Exception $e) {
            Log::error('💥 [CQRS] Failed to sync seance to MongoDB', [
                'seance_uuid' => $seanceUuid,
                'error'       => $e->getMessage(),
                'trace'       => $e->getTraceAsString(),
                'file'        => $e->getFile(),
                'line'        => $e->getLine(),
            ]);

            // Ne pas re-throw pour éviter de faire planter l'app
            // L'erreur est loggée, on peut investiguer plus tard
        }
    }

    /**
     * Handle seance update event
     */
    public function handleSeanceUpdated(SeanceUpdated $event): void
    {
        try {
            $seanceUuid = $event->getSeanceUuid();

            // Charger depuis PostgreSQL avec relations
            $seanceModel = SeanceModel::where('uuid', $seanceUuid)->with(['film', 'salle.cinema'])->first();
            if (!$seanceModel) {
                Log::warning('Seance not found in PostgreSQL for sync', ['seance_uuid' => $seanceUuid]);

                return;
            }

            // ✅ FIX : Utiliser la logique commune
            $this->syncSeanceData($seanceModel, 'updated');

            Log::info('Seance updated in MongoDB', [
                'seance_uuid' => $seanceUuid,
                'event'       => 'updated',
            ]);
        } catch (Exception $e) {
            Log::error('Failed to update seance in MongoDB', [
                'seance_uuid' => $event->getAggregateId(),
                'error'       => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Handle seance deletion event
     */
    public function handleSeanceDeleted(SeanceDeleted $event): void
    {
        try {
            $seanceUuid = $event->getSeanceUuid();

            // Soft delete dans MongoDB
            SeancePublic::where('seance_id', $seanceUuid)->update([
                'statut'     => 'supprimee',
                'deleted_at' => now(),
                'updated_at' => now(),
            ]);

            Log::info('Seance soft deleted in MongoDB', [
                'seance_uuid' => $seanceUuid,
                'event'       => 'deleted',
            ]);
        } catch (Exception $e) {
            Log::error('Failed to soft delete seance in MongoDB', [
                'seance_uuid' => $event->getAggregateId(),
                'error'       => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Handle seance status change event
     */
    public function handleSeanceStatusChanged(SeanceStatusChanged $event): void
    {
        try {
            $seanceUuid = $event->getSeanceUuid();

            // Charger depuis PostgreSQL pour obtenir le nouveau statut
            $seanceModel = SeanceModel::where('uuid', $seanceUuid)->first();
            if (!$seanceModel) {
                Log::warning('Seance not found in PostgreSQL for status sync', ['seance_uuid' => $seanceUuid]);

                return;
            }

            // Mettre à jour le statut dans MongoDB
            $updatedRows = SeancePublic::where('seance_id', $seanceUuid)->update([
                'statut'     => $seanceModel->statut,
                'updated_at' => now(),
            ]);

            Log::info('Seance status updated in MongoDB', [
                'seance_uuid'  => $seanceUuid,
                'new_status'   => $seanceModel->statut,
                'rows_updated' => $updatedRows,
            ]);
        } catch (Exception $e) {
            Log::error('Failed to update seance status in MongoDB', [
                'seance_uuid' => $event->getAggregateId(),
                'error'       => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * ✅ NOUVELLE MÉTHODE : Logique commune de synchronisation
     *
     * Utilise updateOrCreate pour éviter les duplicate key errors
     */
    private function syncSeanceData(SeanceModel $seanceModel, string $eventType = 'sync'): void
    {
        $mongoData = $this->buildMongoData($seanceModel, $eventType);

        // ✅ updateOrCreate = UPSERT (update si existe, create sinon)
        SeancePublic::updateOrCreate(
            ['_id' => $seanceModel->uuid],  // Condition de recherche
            $mongoData                       // Données à sauvegarder
        );
    }

    /**
     * ✅ NOUVELLE MÉTHODE : Construction des données MongoDB
     *
     * Centralise la logique de mapping PostgreSQL → MongoDB
     */
    private function buildMongoData(SeanceModel $seanceModel, string $eventType = 'sync'): array
    {
        $isUpdate = $eventType === 'updated';
        $now      = now();

        $data = [
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
            'taux_tva'                => $seanceModel->taux_tva?->basisPoints ?? 2000, // 20% par défaut
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
            'qualite_projection'      => $seanceModel->qualite_projection ?? 'standard',
            'updated_at'              => $now,
        ];

        // Ajouter created_at seulement si c'est une création
        if (!$isUpdate) {
            $data['created_at'] = $now;
        }

        return $data;
    }

    /**
     * ✅ NOUVELLE MÉTHODE : Normalisation des tarifs
     *
     * Gère les différents types de tarification
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
