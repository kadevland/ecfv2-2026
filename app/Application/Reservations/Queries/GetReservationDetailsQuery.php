<?php

declare(strict_types=1);

namespace App\Application\Reservations\Queries;

use App\Application\Shared\Queries\Query;

final readonly class GetReservationDetailsQuery implements Query
{
    public function __construct(
        public readonly string $reservationId,
        public readonly ?string $userId = null, // Pour sécurité - null si admin
        public readonly bool $includeSeanceDetails = true,
        public readonly bool $includePaiementDetails = true,
        public readonly bool $includeBilletDetails = true,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            reservationId: $data['reservation_id'],
            userId: $data['user_id'] ?? null,
            includeSeanceDetails: $data['include_seance_details'] ?? true,
            includePaiementDetails: $data['include_paiement_details'] ?? true,
            includeBilletDetails: $data['include_billet_details'] ?? true,
        );
    }
}
