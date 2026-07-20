<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * Enum pour les départements/services des employés
 * Organisation type d'une chaîne de cinémas
 */
enum DepartementEnum: string
{
    case ACCUEIL          = 'Accueil';
    case PROJECTION       = 'Projection';
    case TECHNIQUE        = 'Technique';
    case BAR_RESTAURATION = 'Bar/Restauration';
    case DIRECTION        = 'Direction';
    case ADMINISTRATION   = 'Administration';
    case MAINTENANCE      = 'Maintenance';
    case SECURITE         = 'Sécurité';

    /**
     * Retourne toutes les valeurs disponibles
     *
     * @return array<string>
     */
    public static function allValues(): array
    {
        return array_map(fn (self $case) => $case->value, self::cases());
    }

    /**
     * Départements opérationnels (contact client)
     *
     * @return array<self>
     */
    public static function operationalDepartments(): array
    {
        return [
            self::ACCUEIL,
            self::PROJECTION,
            self::BAR_RESTAURATION,
        ];
    }

    /**
     * Départements support
     *
     * @return array<self>
     */
    public static function supportDepartments(): array
    {
        return [
            self::TECHNIQUE,
            self::ADMINISTRATION,
            self::MAINTENANCE,
            self::SECURITE,
        ];
    }
}
