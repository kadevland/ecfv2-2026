<?php

declare(strict_types=1);

namespace App\Domain\Reservations\Events;

use App\Domain\Shared\Events\DomainEvent;
use App\Domain\Reservations\Entities\Reservation;

/**
 * Événement déclenché lors de la finalisation d'une réservation
 * (ReservationCompleted dans la doc MongoDB pour alimenter stats_realtime)
 */
final class ReservationCompleted extends DomainEvent
{
    private function __construct(
        private readonly Reservation $reservation
    ) {
        parent::__construct();
    }

    public static function fromReservation(Reservation $reservation): self
    {
        return new self($reservation);
    }

    public function getEventName(): string
    {
        return 'reservations.reservation.completed';
    }

    public function getAggregateId(): string
    {
        return $this->reservation->id->value;
    }

    public function getAggregateType(): string
    {
        return 'reservation';
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'reservation_id'   => $this->reservation->id->value,
            'seance_id'        => $this->reservation->seanceId->value,
            'user_id'          => $this->reservation->userId->value,
            'nombre_billets'   => $this->reservation->nombrePlaces,
            'montant_total'    => $this->reservation->montantTotal->getAmount(),
            'places_reservees' => $this->reservation->placesDetails,
            'statut'           => $this->reservation->statut,
            'date_reservation' => $this->getOccurredOn()->format('Y-m-d H:i:s'),
        ];
    }

    public function getReservation(): Reservation
    {
        return $this->reservation;
    }

    // Helper methods pour MongoDB stats_realtime
    public function getMontantTotal(): float
    {
        return (float) $this->reservation->montantTotal->getAmount() / 100;
    }

    public function getNombreBillets(): int
    {
        return $this->reservation->nombrePlaces;
    }

    public function getSeanceId(): string
    {
        return $this->reservation->seanceId->value;
    }
}
