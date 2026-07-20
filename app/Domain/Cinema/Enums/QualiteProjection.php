<?php

declare(strict_types=1);

namespace App\Domain\Cinema\Enums;

/**
 * Enum pour les qualités de projection disponibles
 */
enum QualiteProjection: string
{
    case NUMERIQUE_2K = '2K';
    case NUMERIQUE_4K = '4K';
    case IMAX         = 'IMAX';
    case DOLBY_VISION = 'DOLBY_VISION';
    case LASER        = 'LASER';
    case HDR          = 'HDR';

    /**
     * Obtenir toutes les valeurs
     *
     * @return array<string>
     */
    public static function getValues(): array
    {
        return array_map(fn ($case) => $case->value, self::cases());
    }

    /**
     * Obtenir toutes les valeurs avec labels
     *
     * @return array<string, string>
     */
    public static function getOptions(): array
    {
        $options = [];
        foreach (self::cases() as $case) {
            $options[$case->value] = $case->getLabel();
        }

        return $options;
    }

    /**
     * Obtenir le label français
     */
    public function getLabel(): string
    {
        return match ($this) {
            self::NUMERIQUE_2K => '2K Numérique',
            self::NUMERIQUE_4K => '4K Ultra HD',
            self::IMAX         => 'IMAX',
            self::DOLBY_VISION => 'Dolby Vision',
            self::LASER        => 'Projection Laser',
            self::HDR          => 'HDR'
        };
    }
}
