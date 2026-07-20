<?php

declare(strict_types=1);

namespace App\Domain\Shared\Enums;

enum SortDirectionEnum: string
{
    case ASC  = 'asc';
    case DESC = 'desc';

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
            self::ASC  => 'Croissant',
            self::DESC => 'Décroissant',
        };
    }

    /**
     * Obtenir l'icône pour l'affichage
     */
    public function icon(): string
    {
        return match ($this) {
            self::ASC  => '↑',
            self::DESC => '↓',
        };
    }

    /**
     * Obtenir la direction opposée
     */
    public function opposite(): self
    {
        return match ($this) {
            self::ASC  => self::DESC,
            self::DESC => self::ASC,
        };
    }
}
