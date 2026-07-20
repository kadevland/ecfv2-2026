<?php

declare(strict_types=1);

namespace App\Domain\Enums;

enum CodePays: string
{
    case FRANCE     = 'FR';
    case BELGIQUE   = 'BE';
    case LUXEMBOURG = 'LU';
    case SUISSE     = 'CH';
    case CANADA     = 'CA';

    /**
     * @return array<CodePays>
     */
    public static function getPaysOperationnels(): array
    {
        return [self::FRANCE, self::BELGIQUE];
    }

    public function label(): string
    {
        return match ($this) {
            self::FRANCE     => 'France',
            self::BELGIQUE   => 'Belgique',
            self::LUXEMBOURG => 'Luxembourg',
            self::SUISSE     => 'Suisse',
            self::CANADA     => 'Canada',
        };
    }

    public function getFlag(): string
    {
        return match ($this) {
            self::FRANCE     => '🇫🇷',
            self::BELGIQUE   => '🇧🇪',
            self::LUXEMBOURG => '🇱🇺',
            self::SUISSE     => '🇨🇭',
            self::CANADA     => '🇨🇦',
        };
    }

    public function getDevise(): string
    {
        return match ($this) {
            self::FRANCE, self::BELGIQUE, self::LUXEMBOURG => 'EUR',
            self::SUISSE => 'CHF',
            self::CANADA => 'CAD',
        };
    }

    public function getSymboleDevise(): string
    {
        return match ($this) {
            self::FRANCE, self::BELGIQUE, self::LUXEMBOURG => '€',
            self::SUISSE => 'CHF',
            self::CANADA => 'C$',
        };
    }

    public function getLanguePrincipale(): string
    {
        return match ($this) {
            self::FRANCE     => 'fr',
            self::BELGIQUE   => 'fr', // Simplifié pour le français
            self::LUXEMBOURG => 'fr', // Simplifié pour le français
            self::SUISSE     => 'fr', // Simplifié pour le français
            self::CANADA     => 'fr', // Québec français
        };
    }

    public function getTauxTVAStandard(): int
    {
        // Retourne le taux en basis points (ex: 2000 = 20%)
        return match ($this) {
            self::FRANCE     => 2000, // 20%
            self::BELGIQUE   => 2100, // 21%
            self::LUXEMBOURG => 1700, // 17%
            self::SUISSE     => 770, // 7.7%
            self::CANADA     => 1500, // 15% (GST/HST variable selon province)
        };
    }

    public function getTauxTVACulture(): int
    {
        // Taux TVA spécifique culture/spectacles en basis points
        return match ($this) {
            self::FRANCE     => 550, // 5.5% pour spectacles
            self::BELGIQUE   => 600, // 6% pour culture
            self::LUXEMBOURG => 800, // 8% pour culture
            self::SUISSE     => 250, // 2.5% taux réduit
            self::CANADA     => 500, // 5% GST
        };
    }

    public function isEurozone(): bool
    {
        return in_array($this, [self::FRANCE, self::BELGIQUE, self::LUXEMBOURG]);
    }

    public function isFrancophone(): bool
    {
        return true; // Tous les pays supportés parlent français
    }

    public function getFormatTelephone(): string
    {
        return match ($this) {
            self::FRANCE     => '+33 X XX XX XX XX',
            self::BELGIQUE   => '+32 XXX XX XX XX',
            self::LUXEMBOURG => '+352 XX XX XX',
            self::SUISSE     => '+41 XX XXX XX XX',
            self::CANADA     => '+1 XXX XXX XXXX',
        };
    }

    public function getFormatCodePostal(): string
    {
        return match ($this) {
            self::FRANCE     => '00000',
            self::BELGIQUE   => '0000',
            self::LUXEMBOURG => '0000',
            self::SUISSE     => '0000',
            self::CANADA     => 'X0X 0X0',
        };
    }

    public function estOperationnel(): bool
    {
        return in_array($this, self::getPaysOperationnels());
    }
}
