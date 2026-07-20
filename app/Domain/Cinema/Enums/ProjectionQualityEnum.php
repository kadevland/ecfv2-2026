<?php

declare(strict_types=1);

namespace App\Domain\Cinema\Enums;

enum ProjectionQualityEnum: string
{
    case STANDARD_2D = '2d';
    case STANDARD_3D = '3d';
    case IMAX        = 'imax';
    case FOUR_DX     = '4dx';
    case DOLBY_ATMOS = 'dolby_atmos';

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
            self::STANDARD_2D => '2D Standard',
            self::STANDARD_3D => '3D',
            self::IMAX        => 'IMAX',
            self::FOUR_DX     => '4DX',
            self::DOLBY_ATMOS => 'Dolby Atmos',
        };
    }

    /**
     * Obtenir le supplément tarifaire (en %)
     */
    public function getPriceSupplement(): int
    {
        return match ($this) {
            self::STANDARD_2D => 0,
            self::STANDARD_3D => 20,
            self::IMAX        => 30,
            self::FOUR_DX     => 50,
            self::DOLBY_ATMOS => 25,
        };
    }
}
