<?php

declare(strict_types=1);

namespace App\Listeners\MongoDB;

use Illuminate\Events\Dispatcher;
use Illuminate\Support\Facades\DB;
use App\Domain\Reservations\Events\ReservationMade;
use App\Infrastructure\Schemas\MongoDB\StatsSchema;
use App\Infrastructure\Database\ReadModels\SeanceLive;
use App\Domain\Reservations\Events\ReservationCancelled;
use App\Domain\Reservations\Events\ReservationCompleted;
use App\Infrastructure\Schemas\MongoDB\SeanceLiveSchema;
use App\Infrastructure\Database\ReadModels\StatsRealtime;

/**
 * Listener pour synchroniser les événements Reservation vers MongoDB
 */
class ReservationEventListener
{
    /**
     * Enregistre les listeners d'événements Reservation
     */
    public function subscribe(Dispatcher $events): void
    {
        $events->listen(
            'reservations.reservation.made',
            [self::class, 'handleReservationMade']
        );

        $events->listen(
            'reservations.reservation.cancelled',
            [self::class, 'handleReservationCancelled']
        );

        $events->listen(
            'reservations.reservation.completed',
            [self::class, 'handleReservationCompleted']
        );
    }

    /**
     * Gère la création d'une réservation
     */
    public function handleReservationMade(ReservationMade $event): void
    {
        $reservation = $event->getReservation();

        // Mettre à jour les places de la séance
        $this->updateSeancePlaces($reservation->seanceId->value);

        // Mettre à jour les stats temps réel
        $this->updateStatsRealtime();
    }

    /**
     * Gère l'annulation d'une réservation
     */
    public function handleReservationCancelled(ReservationCancelled $event): void
    {
        $reservation = $event->getReservation();

        // Mettre à jour les places de la séance
        $this->updateSeancePlaces($reservation->seanceId->value);

        // Mettre à jour les stats temps réel
        $this->updateStatsRealtime();
    }

    /**
     * Gère la finalisation d'une réservation
     */
    public function handleReservationCompleted(ReservationCompleted $event): void
    {
        $reservation = $event->getReservation();

        // Mettre à jour les places de la séance
        $this->updateSeancePlaces($reservation->seanceId->value);

        // Mettre à jour les stats temps réel avec revenus
        $this->updateStatsRealtime($reservation->montantTotal, $reservation->nombreBillets);
    }

    /**
     * Met à jour les places disponibles d'une séance
     */
    private function updateSeancePlaces(string $seanceId): void
    {
        // Récupérer les données depuis PostgreSQL (write-side)
        $seanceData = DB::connection('pgsql')->table('seances')
            ->where('id', $seanceId)
            ->first(['places_totales', 'places_vendues', 'places_reservees']);

        if ($seanceData) {
            $placesDisponibles = $seanceData->places_totales - $seanceData->places_vendues - $seanceData->places_reservees;

            $updateData = [
                SeanceLiveSchema::PLACES_VENDUES     => $seanceData->places_vendues,
                SeanceLiveSchema::PLACES_RESERVEES   => $seanceData->places_reservees,
                SeanceLiveSchema::PLACES_DISPONIBLES => $placesDisponibles,
                SeanceLiveSchema::UPDATED_AT         => now(),
            ];

            SeanceLive::where(SeanceLiveSchema::SEANCE_ID, $seanceId)
                ->update($updateData);
        }
    }

    /**
     * Met à jour les statistiques temps réel
     */
    private function updateStatsRealtime(?float $montantVente = null, ?int $nombreBillets = null): void
    {
        $today = now()->startOfDay();

        // Récupérer ou créer les stats du jour
        $stats = StatsRealtime::where(StatsSchema::TIMESTAMP, '>=', $today)->first();

        if (!$stats) {
            $documentData = StatsSchema::statsRealtimeStructure([
                StatsSchema::TIMESTAMP              => now(),
                StatsSchema::VENTES_JOUR            => 0,
                StatsSchema::RESERVATIONS_ACTIVES   => 0,
                StatsSchema::TAUX_OCCUPATION_GLOBAL => 0.0,
                StatsSchema::REVENUS_JOUR           => 0.0,
                StatsSchema::FILMS_PERFORMANCE      => [],
                StatsSchema::CINEMAS_PERFORMANCE    => [],
                StatsSchema::ALERTES                => [],
            ]);

            $stats = StatsRealtime::create($documentData);
        }

        // Calculer les nouvelles valeurs depuis PostgreSQL
        $ventesJour = DB::connection('pgsql')->table('reservations')
            ->where('date_reservation', '>=', $today)
            ->where('statut', 'confirmed')
            ->count();

        $reservationsActives = DB::connection('pgsql')->table('reservations')
            ->where('statut', 'pending')
            ->count();

        $revenusJour = DB::connection('pgsql')->table('reservations')
            ->where('date_reservation', '>=', $today)
            ->where('statut', 'confirmed')
            ->sum('montant_total');

        // Calculer le taux d'occupation global
        $tauxOccupation = $this->calculateTauxOccupationGlobal();

        $updateData = [
            StatsSchema::TIMESTAMP              => now(),
            StatsSchema::VENTES_JOUR            => $ventesJour,
            StatsSchema::RESERVATIONS_ACTIVES   => $reservationsActives,
            StatsSchema::REVENUS_JOUR           => (float) $revenusJour,
            StatsSchema::TAUX_OCCUPATION_GLOBAL => $tauxOccupation,
            StatsSchema::UPDATED_AT             => now(),
        ];

        $stats->update($updateData);
    }

    /**
     * Calcule le taux d'occupation global
     */
    private function calculateTauxOccupationGlobal(): float
    {
        $totalPlaces = DB::connection('pgsql')->table('seances')
            ->join('salles', 'seances.salle_id', '=', 'salles.id')
            ->where('seances.date_heure_debut', '>=', now()->startOfDay())
            ->where('seances.date_heure_debut', '<=', now()->endOfDay())
            ->sum('salles.capacite');

        $placesVendues = DB::connection('pgsql')->table('seances')
            ->where('date_heure_debut', '>=', now()->startOfDay())
            ->where('date_heure_debut', '<=', now()->endOfDay())
            ->sum('places_vendues');

        return $totalPlaces > 0 ? ($placesVendues / $totalPlaces) * 100 : 0.0;
    }
}
