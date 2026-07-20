<?php

declare(strict_types=1);

namespace App\Domain\Reservations\Events;

use App\Domain\Shared\Events\DomainEvent;
use App\Domain\Reservations\Entities\Reservation;

final class ReservationCreated extends DomainEvent
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
        return 'reservations.reservation.created';
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
     * Réservations doivent être traitées en SYNCHRONE pour maj immédiate des places
     */
    public function isQueueEvent(): bool
    {
        return false; // SYNCHRONE pour réservations
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'reservation_id'     => $this->reservation->id->value,
            'numero_reservation' => $this->reservation->numeroReservation,
            'user_id'            => $this->reservation->userId->value,
            'seance_id'          => $this->reservation->seanceId->value,
            'nombre_places'      => $this->reservation->nombrePlaces,
            'places_details'     => $this->reservation->placesDetails,
            'montant_total'      => $this->reservation->montantTotal->getAmount(),
            'montant_ht'         => $this->reservation->montantHt->getAmount(),
            'taux_tva'           => $this->reservation->tauxTva->basisPoints,
            'statut'             => $this->reservation->statut,
            'date_expiration'    => $this->reservation->dateExpiration?->format('Y-m-d H:i:s'),
            'commentaires'       => $this->reservation->commentaires,
            'qr_code'            => $this->reservation->qrCode,
        ];
    }

    public function getReservation(): Reservation
    {
        return $this->reservation;
    }
}
