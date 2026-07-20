<?php

declare(strict_types=1);

namespace App\Domain\Reservations\Exceptions;

use Throwable;
use DomainException;

/**
 * Exception pour les erreurs de validation métier des réservations
 */
final class ReservationValidationException extends DomainException
{
    public function __construct(
        string $message,
        int $code = 0,
        ?Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }

    /**
     * Exception pour places insuffisantes
     */
    public static function placesInsuffisantes(int $demandees, int $disponibles): self
    {
        return new self(
            "Seulement {$disponibles} place(s) disponible(s), {$demandees} demandée(s)"
        );
    }

    /**
     * Exception pour séance expirée
     */
    public static function seanceExpiree(): self
    {
        return new self('Impossible de réserver pour une séance passée');
    }

    /**
     * Exception pour délai insuffisant
     */
    public static function delaiInsuffisant(int $heuresMinimum): self
    {
        return new self(
            "Réservations fermées : minimum {$heuresMinimum} heure(s) avant la séance"
        );
    }

    /**
     * Exception pour nombre de places invalide
     */
    public static function nombrePlacesInvalide(int $min, int $max): self
    {
        return new self(
            "Nombre de places invalide : entre {$min} et {$max} places autorisées"
        );
    }
}
