<?php

declare(strict_types=1);

namespace App\Application\Reservations\DTOs;

use DateTimeInterface;

/**
 * DTO pour l'affichage détaillé d'une réservation
 */
final readonly class ReservationDetailDto
{
    /**
     * @param array<string, mixed> $placesDetails
     */
    public function __construct(
        public string $id,
        public string $numeroReservation,
        public string $userId,
        public string $userEmail,
        public string $userNom,
        public string $userPrenom,
        public string $seanceId,
        public string $filmTitre,
        public string|null $filmAffcheUrl,
        public ?DateTimeInterface $seanceDate,
        public string $cinemaName,
        public string $salleName,
        public int $nombrePlaces,
        /** @var array<string, mixed> */ public array $placesDetails,
        public \Money\Money $montantTotal,
        public \Money\Money $montantHt,
        public string $statut,
        public ?DateTimeInterface $dateExpiration,
        public ?string $commentaires,
        public ?string $qrCode,
        public DateTimeInterface $dateCreation,
        public DateTimeInterface $dateModification,
    ) {}
}
