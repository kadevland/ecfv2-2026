<?php

declare(strict_types=1);

namespace App\Application\Reservations\Commands;

use App\Application\Shared\Commands\Command;

final readonly class ValidateTicketCommand implements Command
{
    public function __construct(
        public readonly string $qrCodeData,
        public readonly string $employeId,
        public readonly string $cinemaId,
        public readonly ?string $salleId = null,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            qrCodeData: $data['qr_code_data'],
            employeId: $data['employe_id'],
            cinemaId: $data['cinema_id'],
            salleId: $data['salle_id'] ?? null,
        );
    }
}
