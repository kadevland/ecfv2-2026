<?php

declare(strict_types=1);

namespace App\Domain\Cinema\Enums;

enum TypeSiegeEnum: string
{
    case STANDARD  = 'standard';
    case PREMIUM   = 'premium';
    case CONFORT   = 'confort';
    case VIP       = 'vip';
    case HANDICAPE = 'handicape';

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
            self::STANDARD  => 'Standard',
            self::PREMIUM   => 'Premium',
            self::CONFORT   => 'Confort',
            self::VIP       => 'VIP',
            self::HANDICAPE => 'Accès handicapé',
        };
    }

    /**
     * Obtenir le supplément tarifaire (en %)
     */
    public function getPriceSupplement(): int
    {
        return match ($this) {
            self::STANDARD  => 0,
            self::PREMIUM   => 25,
            self::CONFORT   => 15,
            self::VIP       => 50,
            self::HANDICAPE => 0,
        };
    }

    /**
     * Obtenir la couleur pour l'affichage
     */
    public function color(): string
    {
        return match ($this) {
            self::STANDARD  => 'blue',
            self::PREMIUM   => 'purple',
            self::CONFORT   => 'green',
            self::VIP       => 'gold',
            self::HANDICAPE => 'orange',
        };
    }
}
