<?php

declare(strict_types=1);

namespace App\Application\Cinema\Listeners;

use Exception;
use Illuminate\Support\Facades\Log;
use App\Domain\Cinema\Events\CinemaCreated;
use App\Domain\Cinema\Events\CinemaUpdated;
use App\Domain\Cinema\Events\CinemaStatusChanged;
use App\Infrastructure\Database\ReadModels\CinemaPublic;
use App\Infrastructure\Database\Schemas\Cinema\CinemaPublicSchema;
use App\Infrastructure\Database\Models\Cinema\Cinema as CinemaModel;

/**
 * Synchronise les changements de cinéma vers MongoDB pour le read-side CQRS
 */
final class SyncCinemaToMongoDb
{
    /**
     * Handle cinema creation event
     */
    public function handleCinemaCreated(CinemaCreated $event): void
    {
        try {
            $cinemaUuid = $event->getCinemaUuid();

            // Charger depuis PostgreSQL
            $cinemaModel = CinemaModel::where('uuid', $cinemaUuid)->first();
            if (!$cinemaModel) {
                Log::warning('Cinema not found in PostgreSQL for sync', ['cinema_uuid' => $cinemaUuid]);

                return;
            }

            // Synchroniser vers MongoDB
            CinemaPublic::updateOrCreate(
                [CinemaPublicSchema::CINEMA_ID => $cinemaUuid],
                [
                    CinemaPublicSchema::CINEMA_ID          => $cinemaUuid,
                    CinemaPublicSchema::NOM                => $cinemaModel->nom,
                    CinemaPublicSchema::DESCRIPTION        => $cinemaModel->description,
                    CinemaPublicSchema::ADRESSE            => $cinemaModel->adresse->rue,
                    CinemaPublicSchema::VILLE              => $cinemaModel->adresse->ville,
                    CinemaPublicSchema::CODE_POSTAL        => $cinemaModel->adresse->codePostal,
                    CinemaPublicSchema::PAYS               => $cinemaModel->pays->value,
                    CinemaPublicSchema::TELEPHONE          => $cinemaModel->telephone ? [$cinemaModel->telephone] : [],
                    CinemaPublicSchema::EMAIL              => $cinemaModel->email ? ['value' => $cinemaModel->email] : [],
                    CinemaPublicSchema::STATUT             => $cinemaModel->est_actif ? 'actif' : 'inactif',
                    CinemaPublicSchema::LATITUDE           => $cinemaModel->coordonnees_gps->latitude ?? null,
                    CinemaPublicSchema::LONGITUDE          => $cinemaModel->coordonnees_gps->longitude ?? null,
                    CinemaPublicSchema::SERVICES           => [],
                    CinemaPublicSchema::NOMBRE_SALLES      => 0,
                    CinemaPublicSchema::SALLES             => [],
                    CinemaPublicSchema::HORAIRES_OUVERTURE => $cinemaModel->horaires_ouverture ?? null,
                ]
            );

            Log::info('Cinema synced to MongoDB', [
                'cinema_uuid' => $cinemaUuid,
                'event'       => 'created',
            ]);
        } catch (Exception $e) {

            Log::error('Failed to sync cinema to MongoDB', [
                'cinema_id' => $event->getAggregateId(),
                'error'     => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Handle cinema update event
     */
    public function handleCinemaUpdated(CinemaUpdated $event): void
    {
        try {
            $cinemaUuid = $event->getCinemaUuid();

            // Charger depuis PostgreSQL
            $cinemaModel = CinemaModel::where('uuid', $cinemaUuid)->first();
            if (!$cinemaModel) {
                Log::warning('Cinema not found in PostgreSQL for sync', ['cinema_uuid' => $cinemaUuid]);

                return;
            }

            // Vérifier si le cinéma existe déjà dans MongoDB
            $existingCinema = CinemaPublic::where(CinemaPublicSchema::CINEMA_ID, $cinemaUuid)->first();
            if ($existingCinema) {
                // Mettre à jour
                $existingCinema->update([
                    CinemaPublicSchema::NOM                => $cinemaModel->nom,
                    CinemaPublicSchema::DESCRIPTION        => $cinemaModel->description,
                    CinemaPublicSchema::ADRESSE            => $cinemaModel->adresse->rue,
                    CinemaPublicSchema::VILLE              => $cinemaModel->adresse->ville,
                    CinemaPublicSchema::CODE_POSTAL        => $cinemaModel->adresse->codePostal,
                    CinemaPublicSchema::PAYS               => $cinemaModel->pays->value,
                    CinemaPublicSchema::TELEPHONE          => $cinemaModel->telephone ? [$cinemaModel->telephone] : [],
                    CinemaPublicSchema::EMAIL              => $cinemaModel->email ? ['value' => $cinemaModel->email] : [],
                    CinemaPublicSchema::LATITUDE           => $cinemaModel->coordonnees_gps->latitude ?? null,
                    CinemaPublicSchema::LONGITUDE          => $cinemaModel->coordonnees_gps->longitude ?? null,
                    CinemaPublicSchema::STATUT             => $cinemaModel->est_actif ? 'actif' : 'inactif',
                    CinemaPublicSchema::HORAIRES_OUVERTURE => $cinemaModel->horaires_ouverture ?? null,
                ]);
            } else {
                // Créer s'il n'existe pas
                CinemaPublic::create([
                    CinemaPublicSchema::CINEMA_ID          => $cinemaUuid,
                    CinemaPublicSchema::NOM                => $cinemaModel->nom,
                    CinemaPublicSchema::DESCRIPTION        => $cinemaModel->description,
                    CinemaPublicSchema::ADRESSE            => $cinemaModel->adresse->rue,
                    CinemaPublicSchema::VILLE              => $cinemaModel->adresse->ville,
                    CinemaPublicSchema::CODE_POSTAL        => $cinemaModel->adresse->codePostal,
                    CinemaPublicSchema::PAYS               => $cinemaModel->pays->value,
                    CinemaPublicSchema::TELEPHONE          => $cinemaModel->telephone ? [$cinemaModel->telephone] : [],
                    CinemaPublicSchema::EMAIL              => $cinemaModel->email ? ['value' => $cinemaModel->email] : [],
                    CinemaPublicSchema::STATUT             => $cinemaModel->est_actif ? 'actif' : 'inactif',
                    CinemaPublicSchema::LATITUDE           => $cinemaModel->coordonnees_gps->latitude ?? null,
                    CinemaPublicSchema::LONGITUDE          => $cinemaModel->coordonnees_gps->longitude ?? null,
                    CinemaPublicSchema::SERVICES           => [],
                    CinemaPublicSchema::NOMBRE_SALLES      => 0,
                    CinemaPublicSchema::SALLES             => [],
                    CinemaPublicSchema::HORAIRES_OUVERTURE => $cinemaModel->horaires_ouverture ?? null,
                ]);

                return;
            }

            Log::info('Cinema updated in MongoDB', [
                'cinema_uuid' => $cinemaUuid,
                'event'       => 'updated',
            ]);
        } catch (Exception $e) {
            Log::error('Failed to update cinema in MongoDB', [
                'cinema_uuid' => $event->getCinemaUuid(),
                'error'       => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Handle cinema status change event
     */
    public function handleCinemaStatusChanged(CinemaStatusChanged $event): void
    {
        try {
            $cinemaUuid = $event->getCinemaUuid();

            // Charger depuis PostgreSQL pour obtenir le statut actuel
            $cinemaModel = CinemaModel::where('uuid', $cinemaUuid)->first();
            if (!$cinemaModel) {
                Log::warning('Cinema not found in PostgreSQL for status sync', ['cinema_uuid' => $cinemaUuid]);

                return;
            }

            // Mettre à jour le statut dans MongoDB
            $updatedRows = CinemaPublic::where(CinemaPublicSchema::CINEMA_ID, $cinemaUuid)->update([
                CinemaPublicSchema::STATUT => $cinemaModel->est_actif ? 'actif' : 'inactif',
            ]);

            Log::info('Cinema status updated in MongoDB', [
                'cinema_uuid'  => $cinemaUuid,
                'new_status'   => $cinemaModel->est_actif ? 'actif' : 'inactif',
                'rows_updated' => $updatedRows,
            ]);
        } catch (Exception $e) {
            Log::error('Failed to update cinema status in MongoDB', [
                'cinema_uuid' => $event->getCinemaUuid(),
                'error'       => $e->getMessage(),
            ]);
            throw $e;
        }
    }
}
