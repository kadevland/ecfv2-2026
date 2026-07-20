<?php

declare(strict_types=1);

namespace App\Domain\Reservations\Exceptions;

use DomainException;

final class SeatsNotAvailableException extends DomainException
{
    /**
     * @param array<string> $seats
     */
    public static function seatsAlreadyTaken(array $seats): self
    {
        $seatsStr = implode(', ', $seats);

        return new self("Places déjà réservées : {$seatsStr}");
    }

    public static function notEnoughSeats(int $available): self
    {
        return new self("Seulement {$available} places disponibles");
    }

    public static function seanceFullyBooked(): self
    {
        return new self('Cette séance est complète');
    }
}
