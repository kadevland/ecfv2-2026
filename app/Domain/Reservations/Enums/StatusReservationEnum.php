<?php

declare(strict_types=1);

namespace App\Domain\Reservations\Enums;

enum StatusReservationEnum: string
{
    case EN_ATTENTE = 'en_attente';
    case CONFIRMEE  = 'confirmee';
    case PAYEE      = 'payee';
    case ANNULEE    = 'annulee';
    case EXPIREE    = 'expiree';

    /**
     * Obtenir toutes les valeurs pour les validations
     *
     * @return array<string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Obtenir les options pour les selects HTML
     *
     * @return array<string, string>
     */
    public static function options(): array
    {
        return array_combine(
            self::values(),
            array_map(fn ($case) => $case->label(), self::cases())
        );
    }

    /**
     * Obtenir le libellé français
     */
    public function label(): string
    {
        return match ($this) {
            self::EN_ATTENTE => 'En attente',
            self::CONFIRMEE  => 'Confirmée',
            self::PAYEE      => 'Payée',
            self::ANNULEE    => 'Annulée',
            self::EXPIREE    => 'Expirée',
        };
    }

    /**
     * Obtenir la couleur pour l'affichage
     */
    public function color(): string
    {
        return match ($this) {
            self::EN_ATTENTE => 'yellow',
            self::CONFIRMEE  => 'blue',
            self::PAYEE      => 'green',
            self::ANNULEE    => 'red',
            self::EXPIREE    => 'gray',
        };
    }

    /**
     * Vérifier si la réservation est valide
     */
    public function isValid(): bool
    {
        return in_array($this, [self::CONFIRMEE, self::PAYEE]);
    }

    /**
     * Vérifier si la réservation peut être annulée
     */
    public function canCancel(): bool
    {
        return in_array($this, [self::EN_ATTENTE, self::CONFIRMEE]);
    }
}
