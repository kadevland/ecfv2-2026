<?php

declare(strict_types=1);

namespace App\Domain\Cinema\Enums;

enum StatusCinemaEnum: string
{
    case ACTIF       = 'actif';
    case INACTIF     = 'inactif';
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
            self::ACTIF       => 'Actif',
            self::INACTIF     => 'Inactif',
            self::MAINTENANCE => 'En maintenance',
        };
    }

    /**
     * Obtenir la couleur pour l'affichage
     */
    public function color(): string
    {
        return match ($this) {
            self::ACTIF       => 'green',
            self::INACTIF     => 'red',
            self::MAINTENANCE => 'yellow',
        };
    }

    /**
     * Vérifier si le cinéma est ouvert au public
     */
    public function isOpen(): bool
    {
        return $this === self::ACTIF;
    }
}
