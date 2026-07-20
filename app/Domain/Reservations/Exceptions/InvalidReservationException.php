<?php

declare(strict_types=1);

namespace App\Domain\Reservations\Exceptions;

use DomainException;

final class InvalidReservationException extends DomainException
{
    public static function missingSeats(): self
    {
        return new self('Veuillez sélectionner au moins une place');
    }

    public static function tooManySeats(int $max): self
    {
        return new self("Maximum {$max} places par réservation");
    }

    public static function invalidSeatFormat(string $seat): self
    {
        return new self("Format de siège invalide : {$seat}");
    }

    public static function seanceNotFound(): self
    {
        return new self('Séance non trouvée');
    }

    public static function numberedSeatsRequired(): self
    {
        return new self('Pour cette salle, vous devez sélectionner des places numérotées');
    }

    public static function seatsOrNumberRequired(): self
    {
        return new self('Veuillez sélectionner des places ou indiquer le nombre souhaité');
    }
}
