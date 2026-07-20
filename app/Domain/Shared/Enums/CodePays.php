<?php

declare(strict_types=1);

namespace App\Domain\Shared\Enums;

use ValueError;

/**
 * Codes pays ISO 3166-1 pour cinémas franco-belges
 * France, Belgique et pays limitrophes
 */
enum CodePays: string
{
    case France     = 'FR';
    case Belgique   = 'BE';
    case Allemagne  = 'DE';
    case PaysBas    = 'NL';
    case Luxembourg = 'LU';
    case Suisse     = 'CH';
    case Italie     = 'IT';
    case Espagne    = 'ES';
    case RoyaumeUni = 'GB';

    /**
     * Crée depuis code ISO
     */
    public static function fromCode(string $code): self
    {
        return self::from(strtoupper($code));
    }

    /**
     * Version safe qui retourne null si code invalide
     */
    public static function tryFromCode(?string $code): ?self
    {
        if ($code === null) {
            return null;
        }

        try {
            return self::fromCode($code);
        } catch (ValueError) {
            return null;
        }
    }

    /**
     * Nom complet du pays (utilise les fichiers de langue Laravel)
     */
    public function nomComplet(): string
    {
        return __("pays.{$this->value}");
    }

    /**
     * Indicatif téléphonique du pays
     */
    public function indicatifTelephonique(): int
    {
        return match ($this) {
            self::France     => 33,
            self::Belgique   => 32,
            self::Allemagne  => 49,
            self::PaysBas    => 31,
            self::Luxembourg => 352,
            self::Suisse     => 41,
            self::Italie     => 39,
            self::Espagne    => 34,
            self::RoyaumeUni => 44,
        };
    }
}
