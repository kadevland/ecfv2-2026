<?php

declare(strict_types=1);

namespace App\Application\Reservations\Commands;

use App\Application\Shared\Commands\Command;

final readonly class ConfirmReservationCommand implements Command
{
    public function __construct(
        public readonly string $reservationId,
        public readonly ?string $qrCode = null,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            reservationId: $data['reservation_id'],
            qrCode: $data['qr_code'] ?? null,
        );
    }
}
