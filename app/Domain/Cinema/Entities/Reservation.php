<?php

declare(strict_types=1);

namespace App\Domain\Cinema\Entities;

use DateTime;
use App\Domain\Shared\ValueObjects\Money;
use App\Domain\Cinema\ValueObjects\SeanceId;
use App\Domain\Cinema\ValueObjects\ReservationId;
use App\Domain\Cinema\ValueObjects\UtilisateurId;

/**
 * Entité Reservation - Stub basique pour éviter les erreurs de types
 * TODO: Implémenter complètement avec tous les attributs et règles métier
 */
final class Reservation
{
    public function __construct(
        public readonly ReservationId $id,
        public readonly SeanceId $seanceId,
        public readonly UtilisateurId $utilisateurId,
        public readonly int $nombrePlaces,
        public readonly Money $montantTotal,
        public readonly string $statut,
        public readonly DateTime $dateReservation,
        public readonly ?DateTime $dateExpiration = null,
    ) {}

    public static function create(
        ReservationId $id,
        SeanceId $seanceId,
        UtilisateurId $utilisateurId,
        int $nombrePlaces,
        Money $montantTotal,
        string $statut = 'en_attente'
    ): self {
        return new self(
            id: $id,
            seanceId: $seanceId,
            utilisateurId: $utilisateurId,
            nombrePlaces: $nombrePlaces,
            montantTotal: $montantTotal,
            statut: $statut,
            dateReservation: new DateTime,
            dateExpiration: (new DateTime)->modify('+15 minutes')
        );
    }
}
