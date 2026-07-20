<?php

declare(strict_types=1);

namespace App\Application\Reservations\Listeners;

use DateTime;
use Exception;
use Illuminate\Support\Facades\Log;
use App\Domain\Reservations\Events\ReservationCreated;
use App\Domain\Reservations\Events\ReservationCancelled;
use App\Domain\Reservations\Events\ReservationConfirmed;
use App\Infrastructure\Database\ReadModels\ReservationPublic as ReservationMongoDB;

/**
 * Synchronise les réservations vers MongoDB en mode SYNCHRONE
 * Traitement en temps réel pour mise à jour immédiate des places disponibles
 */
final class SyncReservationToMongoDb
{
    /**
     * Handle reservation creation event (SYNCHRONE)
     * Traitement immédiat pour mise à jour en temps réel des places
     */
    public function handleReservationCreated(ReservationCreated $event): void
    {
        try {
            $data = $event->toArray();

            ReservationMongoDB::create([
                '_id'                => $data['reservation_id'],
                'reservation_id'     => $data['reservation_id'],
                'numero_reservation' => $data['numero_reservation'],
                'user_id'            => $data['user_id'],
                'seance_id'          => $data['seance_id'],
                'nombre_places'      => $data['nombre_places'],
                'places_details'     => $data['places_details'],
                'montant_total'      => $data['montant_total'],
                'montant_ht'         => $data['montant_ht'],
                'taux_tva'           => $data['taux_tva'],
                'statut'             => $data['statut'],
                'date_expiration'    => $data['date_expiration'] ? new DateTime($data['date_expiration']) : null,
                'commentaires'       => $data['commentaires'],
                'qr_code'            => $data['qr_code'],
                'created_at'         => now(),
                'updated_at'         => now(),
            ]);

            Log::info('Reservation created in MongoDB (SYNC)', [
                'reservation_id' => $data['reservation_id'],
                'processing'     => 'synchronous',
            ]);
        } catch (Exception $e) {
            Log::error('Failed to create reservation in MongoDB (SYNC)', [
                'reservation_id' => $event->getAggregateId(),
                'error'          => $e->getMessage(),
            ]);
            // Re-throw pour traitement synchrone - échec immédiat requis
            throw $e;
        }
    }

    /**
     * Handle reservation confirmation event (SYNCHRONE)
     * Confirmation immédiate pour mise à jour en temps réel
     */
    public function handleReservationConfirmed(ReservationConfirmed $event): void
    {
        try {
            $data = $event->toArray();

            ReservationMongoDB::where('_id', $data['reservation_id'])->update([
                'statut'          => $data['statut'],
                'date_expiration' => $data['date_expiration'] ? new DateTime($data['date_expiration']) : null,
                'commentaires'    => $data['commentaires'],
                'qr_code'         => $data['qr_code'],
                'updated_at'      => now(),
            ]);

            Log::info('Reservation confirmed in MongoDB (SYNC)', [
                'reservation_id' => $data['reservation_id'],
                'processing'     => 'synchronous',
            ]);
        } catch (Exception $e) {
            Log::error('Failed to confirm reservation in MongoDB (SYNC)', [
                'reservation_id' => $event->getAggregateId(),
                'error'          => $e->getMessage(),
            ]);
            // Re-throw pour traitement synchrone - échec immédiat requis
            throw $e;
        }
    }

    /**
     * Handle reservation cancellation event (SYNCHRONE)
     * Annulation immédiate pour libération en temps réel des places
     */
    public function handleReservationCancelled(ReservationCancelled $event): void
    {
        try {
            $data = $event->toArray();

            ReservationMongoDB::where('_id', $data['reservation_id'])->update([
                'statut'       => $data['statut'],
                'commentaires' => $data['commentaires'],
                'updated_at'   => now(),
            ]);

            Log::info('Reservation cancelled in MongoDB (SYNC)', [
                'reservation_id' => $data['reservation_id'],
                'processing'     => 'synchronous',
            ]);
        } catch (Exception $e) {
            Log::error('Failed to cancel reservation in MongoDB (SYNC)', [
                'reservation_id' => $event->getAggregateId(),
                'error'          => $e->getMessage(),
            ]);
            // Re-throw pour traitement synchrone - échec immédiat requis
            throw $e;
        }
    }
}
