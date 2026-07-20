<?php

declare(strict_types=1);

namespace App\Domain\Shared\Enums;

enum SexeEnum: string
{
    case MASCULIN     = 'M';
    case FEMININ      = 'F';
    case AUTRE        = 'AUTRE';
    case NON_SPECIFIE = 'NON_SPECIFIE';

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
            self::MASCULIN     => 'Masculin',
            self::FEMININ      => 'Féminin',
            self::AUTRE        => 'Autre',
            self::NON_SPECIFIE => 'Non spécifié',
        };
    }
}
