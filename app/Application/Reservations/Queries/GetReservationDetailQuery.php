<?php

declare(strict_types=1);

namespace App\Application\Reservations\Queries;

use App\Application\Contracts\QueryInterface;

/**
 * Query pour récupérer le détail d'une réservation
 */
final class GetReservationDetailQuery implements QueryInterface
{
    public function __construct(
        public readonly string $reservationUuid,
    ) {}

    public function isValid(): bool
    {
        return !empty($this->reservationUuid) &&
               preg_match('/^[a-f0-9]{8}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{12}$/i', $this->reservationUuid);
    }

    /**
     * @return array<string, string>
     */
    public function validate(): array
    {
        $errors = [];

        if (empty($this->reservationUuid)) {
            $errors['reservationUuid'] = 'L\'UUID de la réservation est obligatoire.';
        } elseif (!preg_match('/^[a-f0-9]{8}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{12}$/i', $this->reservationUuid)) {
            $errors['reservationUuid'] = 'L\'UUID de la réservation est invalide.';
        }

        return $errors;
    }
}
