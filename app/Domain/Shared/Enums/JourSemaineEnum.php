<?php

declare(strict_types=1);

namespace App\Domain\Shared\Enums;

enum JourSemaineEnum: string
{
    case LUNDI    = 'lundi';
    case MARDI    = 'mardi';
    case MERCREDI = 'mercredi';
    case JEUDI    = 'jeudi';
    case VENDREDI = 'vendredi';
    case SAMEDI   = 'samedi';
    case DIMANCHE = 'dimanche';

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
            self::LUNDI    => 'Lundi',
            self::MARDI    => 'Mardi',
            self::MERCREDI => 'Mercredi',
            self::JEUDI    => 'Jeudi',
            self::VENDREDI => 'Vendredi',
            self::SAMEDI   => 'Samedi',
            self::DIMANCHE => 'Dimanche',
        };
    }

    /**
     * Obtenir le libellé court (3 lettres)
     */
    public function shortLabel(): string
    {
        return match ($this) {
            self::LUNDI    => 'Lun',
            self::MARDI    => 'Mar',
            self::MERCREDI => 'Mer',
            self::JEUDI    => 'Jeu',
            self::VENDREDI => 'Ven',
            self::SAMEDI   => 'Sam',
            self::DIMANCHE => 'Dim',
        };
    }

    /**
     * Obtenir le numéro ISO du jour (1=Lundi, 7=Dimanche)
     */
    public function isoNumber(): int
    {
        return match ($this) {
            self::LUNDI    => 1,
            self::MARDI    => 2,
            self::MERCREDI => 3,
            self::JEUDI    => 4,
            self::VENDREDI => 5,
            self::SAMEDI   => 6,
            self::DIMANCHE => 7,
        };
    }

    /**
     * Vérifier si c'est un jour de weekend
     */
    public function isWeekend(): bool
    {
        return in_array($this, [self::SAMEDI, self::DIMANCHE]);
    }
}
