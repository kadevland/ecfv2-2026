<?php

declare(strict_types=1);

namespace App\Domain\Cinema\Enums;

enum VersionFilmEnum: string
{
    case VF     = 'vf';
    case VO     = 'vo';
    case VOSTFR = 'vostfr';

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
            self::VF     => 'Version française',
            self::VO     => 'Version originale',
            self::VOSTFR => 'Version originale sous-titrée français',
        };
    }

    /**
     * Obtenir le libellé court
     */
    public function shortLabel(): string
    {
        return match ($this) {
            self::VF     => 'VF',
            self::VO     => 'VO',
            self::VOSTFR => 'VOSTFR',
        };
    }
}
