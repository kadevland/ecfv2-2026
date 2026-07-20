<?php

declare(strict_types=1);

namespace App\Application\Reservations\Queries;

use App\Application\Contracts\QueryInterface;

/**
 * Query pour récupérer une réservation par son numéro
 */
final readonly class GetReservationByNumberQuery implements QueryInterface
{
    public function __construct(
        public string $numeroReservation,
    ) {}

    public function isValid(): bool
    {
        return !empty($this->numeroReservation);
    }

    /**
     * @return array<string, string>
     */
    public function validate(): array
    {
        $errors = [];
        if (empty($this->numeroReservation)) {
            $errors['numeroReservation'] = 'Le numéro de réservation est requis';
        }

        return $errors;
    }
}
