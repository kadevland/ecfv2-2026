<?php

declare(strict_types=1);

namespace App\Domain\Reservations\Entities;

use DateTime;
use Money\Money;
use DomainException;
use DateTimeInterface;
use App\Domain\User\ValueObjects\UserId;
use App\Domain\Shared\ValueObjects\TauxTva;
use App\Domain\Cinema\ValueObjects\SeanceId;
use App\Domain\Shared\Entities\AggregateRoot;
use App\Domain\Reservations\Events\ReservationCreated;
use App\Domain\Reservations\ValueObjects\ReservationId;
use App\Domain\Reservations\Events\ReservationCancelled;
use App\Domain\Reservations\Events\ReservationConfirmed;

/**
 * @property ReservationId $id
 * @property string $numeroReservation
 * @property UserId $userId
 * @property SeanceId $seanceId
 * @property int $nombrePlaces
 * @property array<string, mixed> $placesDetails
 * @property Money $montantTotal
 * @property Money $montantHt
 * @property TauxTva $tauxTva
 * @property string $statut
 * @property DateTimeInterface|null $dateExpiration
 * @property string|null $commentaires
 * @property string|null $qrCode
 * @property DateTimeInterface $createdAt
 * @property DateTimeInterface $updatedAt
 */
final class Reservation extends AggregateRoot
{
    public readonly ReservationId $id;

    public private(set) string $numeroReservation;

    public private(set) UserId $userId;

    public private(set) SeanceId $seanceId;

    public private(set) int $nombrePlaces;

    /** @var array<string, mixed> */
    public private(set) array $placesDetails;

    public private(set) Money $montantTotal;

    public private(set) Money $montantHt;

    public private(set) TauxTva $tauxTva;

    public private(set) string $statut;

    public private(set) ?DateTimeInterface $dateExpiration;

    public private(set) ?string $commentaires;

    public private(set) ?string $qrCode;

    /**
     * @param array<string, mixed> $placesDetails
     */
    public function __construct(
        ReservationId $id,
        string $numeroReservation,
        UserId $userId,
        SeanceId $seanceId,
        int $nombrePlaces,
        array $placesDetails,
        Money $montantTotal,
        Money $montantHt,
        TauxTva $tauxTva,
        string $statut = 'en_attente',
        ?DateTimeInterface $dateExpiration = null,
        ?string $commentaires = null,
        ?string $qrCode = null,
    ) {
        $this->id                = $id;
        $this->numeroReservation = $numeroReservation;
        $this->userId            = $userId;
        $this->seanceId          = $seanceId;
        $this->nombrePlaces      = $nombrePlaces;
        $this->placesDetails     = $placesDetails;
        $this->montantTotal      = $montantTotal;
        $this->montantHt         = $montantHt;
        $this->tauxTva           = $tauxTva;
        $this->statut            = $statut;
        $this->dateExpiration    = $dateExpiration;
        $this->commentaires      = $commentaires;
        $this->qrCode            = $qrCode;
    }

    /**
     * @param array<string, mixed> $placesDetails
     */
    public static function creer(
        string $numeroReservation,
        UserId $userId,
        SeanceId $seanceId,
        int $nombrePlaces,
        array $placesDetails,
        Money $montantTotal,
        Money $montantHt,
        TauxTva $tauxTva,
        ?DateTimeInterface $dateExpiration = null,
        ?string $commentaires = null,
    ): self {
        $reservation = new self(
            ReservationId::generate(),
            $numeroReservation,
            $userId,
            $seanceId,
            $nombrePlaces,
            $placesDetails,
            $montantTotal,
            $montantHt,
            $tauxTva,
            'en_attente_paiement',
            $dateExpiration,
            $commentaires,
        );

        $reservation->addDomainEvent(ReservationCreated::fromReservation($reservation));

        return $reservation;
    }

    public function confirmer(): void
    {
        if ($this->statut !== 'en_attente') {
            throw new DomainException('Seules les réservations en attente peuvent être confirmées');
        }

        $this->statut = 'confirmee';
        $this->addDomainEvent(ReservationConfirmed::fromReservation($this));
    }

    public function annuler(?string $raison = null): void
    {
        if ($this->statut === 'annulee') {
            throw new DomainException('La réservation est déjà annulée');
        }

        $this->statut       = 'annulee';
        $this->commentaires = $raison;
        $this->addDomainEvent(ReservationCancelled::fromReservation($this));
    }

    public function isConfirmed(): bool
    {
        return $this->statut === 'confirmee';
    }

    public function isCancelled(): bool
    {
        return $this->statut === 'annulee';
    }

    public function isExpired(): bool
    {
        return $this->dateExpiration && $this->dateExpiration < new DateTime;
    }

    public function genererQrCode(): string
    {
        if (!$this->qrCode) {
            $qrData = [
                'reservation_id' => $this->id->value,
                'numero'         => $this->numeroReservation,
                'seance_id'      => $this->seanceId->value,
                'user_id'        => $this->userId->value,
                'places'         => $this->nombrePlaces,
                'timestamp'      => time(),
            ];

            $this->qrCode = base64_encode(json_encode($qrData));
            $this->addDomainEvent(ReservationConfirmed::fromReservation($this));
        }

        return $this->qrCode;
    }

    /**
     * @return array<string>
     */
    public function getSeatNumbers(): array
    {
        $seats = [];
        foreach ($this->placesDetails['places'] ?? [] as $place) {
            $seats[] = $place['rangee'] . $place['numero'];
        }

        return $seats;
    }

    public function changerDateExpiration(?DateTimeInterface $nouvelleDateExpiration): void
    {
        $this->dateExpiration = $nouvelleDateExpiration;
        $this->addDomainEvent(ReservationConfirmed::fromReservation($this));
    }

    public function ajouterCommentaire(string $commentaire): void
    {
        $this->commentaires = $commentaire;
        $this->addDomainEvent(ReservationConfirmed::fromReservation($this));
    }

    public function calculerMontantTtc(): Money
    {
        return $this->montantHt->add(
            $this->montantHt->multiply((int) ($this->tauxTva->getPercentage() / 100 * 100))
        );
    }

    /**
     * Marque la réservation comme payée
     */
    public function markAsPaid(): void
    {
        if (!in_array($this->statut, ['en_attente_paiement', 'confirmee'])) {
            throw new DomainException(
                "La réservation ne peut pas être marquée comme payée (statut actuel: {$this->statut})"
            );
        }

        $this->statut = 'payee';
        // L'événement sera dispatché par le repository
    }
}
