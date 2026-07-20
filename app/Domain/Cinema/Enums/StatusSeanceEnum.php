<?php

declare(strict_types=1);

namespace App\Domain\Cinema\Enums;

enum StatusSeanceEnum: string
{
    case ACTIVE      = 'active';
    case CANCELLED   = 'cancelled';
    case COMPLETED   = 'completed';
    case MAINTENANCE = 'maintenance';

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
            self::ACTIVE      => 'Active',
            self::CANCELLED   => 'Annulée',
            self::COMPLETED   => 'Terminée',
            self::MAINTENANCE => 'Maintenance',
        };
    }

    /**
     * Obtenir la couleur pour l'affichage
     */
    public function color(): string
    {
        return match ($this) {
            self::ACTIVE      => 'green',
            self::CANCELLED   => 'red',
            self::COMPLETED   => 'gray',
            self::MAINTENANCE => 'yellow',
        };
    }

    /**
     * Vérifier si on peut réserver
     */
    public function canBook(): bool
    {
        return $this === self::ACTIVE;
    }
}
