<?php

declare(strict_types=1);

namespace App\Application\Reservations\Queries;

use App\Application\Contracts\QueryInterface;

final readonly class GetReservationQuery implements QueryInterface
{
    public function __construct(
        public readonly ?string $reservationId = null,
        public readonly ?string $numeroReservation = null,
    ) {}

    public function isValid(): bool
    {
        return !empty($this->reservationId) || !empty($this->numeroReservation);
    }

    /**
     * @return array<string, string>
     */
    public function validate(): array
    {
        $errors = [];
        if (empty($this->reservationId) && empty($this->numeroReservation)) {
            $errors['criteria'] = 'L\'identifiant ou le numéro de réservation est requis';
        }

        return $errors;
    }
}
