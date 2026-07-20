<?php

declare(strict_types=1);

namespace App\Application\Reservations\Commands;

use App\Application\Shared\Commands\Command;

final readonly class CancelReservationCommand implements Command
{
    public function __construct(
        public readonly string $reservationId,
        public readonly string $userId, // Pour sécurité
        public readonly ?string $raison = null,
        public readonly bool $autoriseRemboursement = false,
        public readonly ?string $employeId = null, // Si annulé par employé
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            reservationId: $data['reservation_id'],
            userId: $data['user_id'],
            raison: $data['raison'] ?? null,
            autoriseRemboursement: $data['autorise_remboursement'] ?? false,
            employeId: $data['employe_id'] ?? null,
        );
    }
}
