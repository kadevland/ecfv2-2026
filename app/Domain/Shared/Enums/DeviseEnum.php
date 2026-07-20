<?php

declare(strict_types=1);

namespace App\Domain\Shared\Enums;

enum DeviseEnum: string
{
    case EUR = 'EUR';
    case CHF = 'CHF';
    case CAD = 'CAD';
    case USD = 'USD';

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
            array_map(fn ($case) => $case->label() . ' (' . $case->symbol() . ')', self::cases())
        );
    }

    /**
     * Devise par défaut
     */
    public static function default(): self
    {
        return self::EUR;
    }

    /**
     * Obtenir le symbole de la devise
     */
    public function symbol(): string
    {
        return match ($this) {
            self::EUR => '€',
            self::CHF => 'CHF',
            self::CAD => 'CA$',
            self::USD => '$',
        };
    }

    /**
     * Obtenir le nom complet
     */
    public function label(): string
    {
        return match ($this) {
            self::EUR => 'Euro',
            self::CHF => 'Franc suisse',
            self::CAD => 'Dollar canadien',
            self::USD => 'Dollar américain',
        };
    }

    /**
     * Obtenir le nombre de décimales
     */
    public function decimals(): int
    {
        return 2; // Toutes nos devises ont 2 décimales
    }
}
