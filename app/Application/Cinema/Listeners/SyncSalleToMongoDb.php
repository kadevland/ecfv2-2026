<?php

declare(strict_types=1);

namespace App\Application\Cinema\Listeners;

use Exception;
use Illuminate\Support\Facades\Log;
use App\Domain\Cinema\Events\SalleCreated;
use App\Domain\Cinema\Events\SalleDeleted;
use App\Domain\Cinema\Events\SalleUpdated;
use App\Infrastructure\Database\ReadModels\SallePublic;
use App\Infrastructure\Database\ReadModels\CinemaPublic;
use App\Infrastructure\Database\Models\Cinema\Salle as SalleModel;
use App\Infrastructure\Database\Schemas\Cinema\CinemaPublicSchema;

/**
 * Synchronise les changements de salle vers MongoDB pour le read-side CQRS
 */
final class SyncSalleToMongoDb
{
    /**
     * Handle salle creation event
     */
    public function handleSalleCreated(SalleCreated $event): void
    {
        try {
            $salleUuid = $event->getSalleUuid();

            // Charger depuis PostgreSQL
            $salleModel = SalleModel::where('uuid', $salleUuid)->first();
            if (!$salleModel) {
                Log::warning('Salle not found in PostgreSQL for sync', ['salle_uuid' => $salleUuid]);

                return;
            }

            // Synchroniser vers MongoDB
            SallePublic::create([
                '_id'                => $salleUuid,
                'salle_id'           => $salleUuid,
                'cinema_id'          => $salleModel->cinema_uuid,
                'nom'                => $salleModel->nom,
                'capacite_totale'    => $salleModel->capacite_totale,
                'nombre_rangees'     => $salleModel->nombre_rangees,
                'places_par_rangee'  => $salleModel->places_par_rangee,
                'places_standard'    => $salleModel->places_standard,
                'places_pmr'         => $salleModel->places_pmr,
                'qualite_projection' => $salleModel->qualite_projection,
                'qualite_sonore'     => $salleModel->qualite_sonore,
                'accessibilite_pmr'  => $salleModel->accessibilite_pmr,
                'climatisation'      => $salleModel->climatisation,
                'plan_salle'         => $salleModel->plan_salle,
                'statut'             => $salleModel->statut,
                'created_at'         => now(),
                'updated_at'         => now(),
            ]);

            // Mettre à jour aussi les salles dénormalisées dans le document Cinema
            $this->updateCinemaSalles($salleModel->cinema_uuid);

            Log::info('Salle synced to MongoDB', [
                'salle_uuid' => $salleUuid,
                'event'      => 'created',
            ]);
        } catch (Exception $e) {
            Log::error('Failed to sync salle to MongoDB', [
                'salle_uuid' => $event->getAggregateId(),
                'error'      => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Handle salle update event
     */
    public function handleSalleUpdated(SalleUpdated $event): void
    {
        try {
            $salleUuid = $event->getSalleUuid();

            // Charger depuis PostgreSQL
            $salleModel = SalleModel::where('uuid', $salleUuid)->first();
            if (!$salleModel) {
                Log::warning('Salle not found in PostgreSQL for sync', ['salle_uuid' => $salleUuid]);

                return;
            }

            // Vérifier si la salle existe déjà dans MongoDB
            $existingSalle = SallePublic::where('salle_id', $salleUuid)->first();
            if ($existingSalle) {
                // Mettre à jour
                $existingSalle->update([
                    'nom'                => $salleModel->nom,
                    'capacite_totale'    => $salleModel->capacite_totale,
                    'nombre_rangees'     => $salleModel->nombre_rangees,
                    'places_par_rangee'  => $salleModel->places_par_rangee,
                    'places_standard'    => $salleModel->places_standard,
                    'places_pmr'         => $salleModel->places_pmr,
                    'qualite_projection' => $salleModel->qualite_projection,
                    'qualite_sonore'     => $salleModel->qualite_sonore,
                    'accessibilite_pmr'  => $salleModel->accessibilite_pmr,
                    'climatisation'      => $salleModel->climatisation,
                    'plan_salle'         => $salleModel->plan_salle,
                    'statut'             => $salleModel->statut,
                    'updated_at'         => now(),
                ]);
            } else {
                // Créer s'il n'existe pas
                SallePublic::create([
                    '_id'                => $salleUuid,
                    'salle_id'           => $salleUuid,
                    'cinema_id'          => $salleModel->cinema_uuid,
                    'nom'                => $salleModel->nom,
                    'capacite_totale'    => $salleModel->capacite_totale,
                    'nombre_rangees'     => $salleModel->nombre_rangees,
                    'places_par_rangee'  => $salleModel->places_par_rangee,
                    'places_standard'    => $salleModel->places_standard,
                    'places_pmr'         => $salleModel->places_pmr,
                    'qualite_projection' => $salleModel->qualite_projection,
                    'qualite_sonore'     => $salleModel->qualite_sonore,
                    'accessibilite_pmr'  => $salleModel->accessibilite_pmr,
                    'climatisation'      => $salleModel->climatisation,
                    'plan_salle'         => $salleModel->plan_salle,
                    'statut'             => $salleModel->statut,
                    'created_at'         => now(),
                    'updated_at'         => now(),
                ]);
            }

            // Mettre à jour aussi les salles dénormalisées dans le document Cinema
            $this->updateCinemaSalles($salleModel->cinema_uuid);

            Log::info('Salle updated in MongoDB', [
                'salle_uuid' => $salleUuid,
                'event'      => 'updated',
            ]);
        } catch (Exception $e) {
            Log::error('Failed to update salle in MongoDB', [
                'salle_uuid' => $event->getAggregateId(),
                'error'      => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Handle salle deletion event
     */
    public function handleSalleDeleted(SalleDeleted $event): void
    {
        try {
            $salleUuid = $event->getSalleUuid();

            // Récupérer le cinema_id avant soft delete
            $existingSalle = SallePublic::where('salle_id', $salleUuid)->first();
            if (!$existingSalle) {
                Log::warning('Salle not found in MongoDB for deletion', ['salle_uuid' => $salleUuid]);

                return;
            }

            $cinemaId = $existingSalle->cinema_id;

            // Soft delete dans MongoDB
            SallePublic::where('salle_id', $salleUuid)->update([
                'statut'     => 'supprimee',
                'deleted_at' => now(),
                'updated_at' => now(),
            ]);

            // Mettre à jour aussi les salles dénormalisées dans le document Cinema
            $this->updateCinemaSalles($cinemaId);

            Log::info('Salle soft deleted in MongoDB', [
                'salle_uuid' => $salleUuid,
                'event'      => 'deleted',
            ]);
        } catch (Exception $e) {
            Log::error('Failed to soft delete salle in MongoDB', [
                'salle_uuid' => $event->getAggregateId(),
                'error'      => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Met à jour les salles dénormalisées dans le document Cinema
     */
    private function updateCinemaSalles(string $cinemaId): void
    {
        try {
            // Trouver le cinema
            $cinema = CinemaPublic::where(CinemaPublicSchema::CINEMA_ID, $cinemaId)->first();

            // dump('updateCinemaSalles - cinema found:', $cinema ? 'YES' : 'NO');
            if (!$cinema) {
                // dump('updateCinemaSalles - Cinema NOT FOUND!');
                Log::warning('Cinema not found for denormalization', ['cinema_id' => $cinemaId]);

                return;
            }

            // Récupérer toutes les salles pour ce cinéma
            $salles = SallePublic::where('cinema_id', $cinemaId)->get();

            // Construire le tableau dénormalisé avec TOUS les champs nécessaires pour le read-side
            // dump('updateCinemaSalles - salles count:', $salles->count());
            // dump('updateCinemaSalles - première salle:', $salles->first()?->toArray());
            $sallesArray = $salles->map(function ($salle) {
                return [
                    'uuid'               => $salle->salle_id,
                    'nom'                => $salle->nom,
                    'capacite_totale'    => $salle->capacite_totale,
                    'capacite_pmr'       => $salle->places_pmr ?? 0,
                    'capacite_standard'  => $salle->places_standard ?? 0,
                    'qualite_projection' => $salle->qualite_projection ?? [],
                    'qualite_sonore'     => $salle->qualite_sonore ?? [],
                    'accessibilite_pmr'  => $salle->accessibilite_pmr ?? false,
                    'climatisation'      => $salle->climatisation ?? false,
                    'statut'             => $salle->statut,
                ];
            })->toArray();

            // dump('updateCinemaSalles - sallesArray final:', $sallesArray);

            // Calculer le total des places
            $totalPlaces = $salles->sum('capacite_totale');

            // Mettre à jour le cinema avec les salles dénormalisées, le nombre de salles ET le total des places
            $updateResult = $cinema->update([
                'salles'        => $sallesArray,
                'nombre_salles' => count($sallesArray),
                'total_places'  => $totalPlaces,
            ]);

            // dump('updateCinemaSalles - update result:', $updateResult);

            Log::info('Cinema salles denormalization updated', [
                'cinema_id'    => $cinemaId,
                'salles_count' => count($sallesArray),
            ]);

        } catch (Exception $e) {
            Log::error('Failed to update cinema salles denormalization', [
                'cinema_id' => $cinemaId,
                'error'     => $e->getMessage(),
            ]);
        }
    }
}
