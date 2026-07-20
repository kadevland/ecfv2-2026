<?php

declare(strict_types=1);

namespace App\Domain\Cinema\Enums;

enum ClassificationFilmEnum: string
{
    case TOUS_PUBLICS  = 'TOUS_PUBLICS';
    case AVERTISSEMENT = 'AVERTISSEMENT';
    case MOINS_12      = 'MOINS_12';
    case MOINS_16      = 'MOINS_16';
    case MOINS_18      = 'MOINS_18';

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
            self::TOUS_PUBLICS  => 'Tous publics',
            self::AVERTISSEMENT => 'Avertissement',
            self::MOINS_12      => '-12 ans',
            self::MOINS_16      => '-16 ans',
            self::MOINS_18      => '-18 ans',
        };
    }

    /**
     * Obtenir l'âge minimum requis
     */
    public function getMinimumAge(): ?int
    {
        return match ($this) {
            self::TOUS_PUBLICS  => null,
            self::AVERTISSEMENT => null,
            self::MOINS_12      => 12,
            self::MOINS_16      => 16,
            self::MOINS_18      => 18,
        };
    }
}
