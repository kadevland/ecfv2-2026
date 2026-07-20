<?php

declare(strict_types=1);

namespace App\Domain\Cinema\Enums;

/**
 * Enum pour les qualités sonores disponibles
 */
enum QualiteSonore: string
{
    case DOLBY_SURROUND = 'DOLBY_SURROUND';
    case DOLBY_ATMOS    = 'DOLBY_ATMOS';
    case DTS            = 'DTS';
    case DTS_X          = 'DTS_X';
    case IMAX_ENHANCED  = 'IMAX_ENHANCED';

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
            self::DOLBY_SURROUND => 'Dolby Surround',
            self::DOLBY_ATMOS    => 'Dolby Atmos',
            self::DTS            => 'DTS',
            self::DTS_X          => 'DTS:X',
            self::IMAX_ENHANCED  => 'IMAX Enhanced',
        };
    }
}
