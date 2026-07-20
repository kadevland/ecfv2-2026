<?php

declare(strict_types=1);

namespace App\Domain\Enums;

enum TypeTarif: string
{
    case NORMAL = 'normal';
    case REDUIT = 'reduit';
    case ENFANT = 'enfant';

    /**
     * Tous les tarifs par défaut pour les formulaires
     * TODO: À terme depuis config/BDD/cinéma
     *
     * @return array<string, string>
     */
    public static function getDefaultPrices(): array
    {
        $defaults = [];
        foreach (self::cases() as $tarif) {
            $defaults[$tarif->value] = $tarif->getDefaultPrice();
        }

        return $defaults;
    }

    public function label(): string
    {
        return match ($this) {
            self::NORMAL => 'Tarif normal',
            self::REDUIT => 'Tarif réduit',
            self::ENFANT => 'Tarif enfant',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::NORMAL => 'Tarif plein pour adultes',
            self::REDUIT => 'Tarif réduit (étudiants, chômeurs, cartes privilège)',
            self::ENFANT => 'Tarif enfant (moins de 12 ans)',
        };
    }

    public function getEligibilityInfo(): string
    {
        return match ($this) {
            self::NORMAL => 'Aucune condition',
            self::REDUIT => 'Justificatif requis (carte étudiant, Pôle Emploi, etc.)',
            self::ENFANT => 'Justificatif d\'âge requis (moins de 12 ans)',
        };
    }

    public function requiresProof(): bool
    {
        return $this !== self::NORMAL;
    }

    public function hasAgeRequirement(): bool
    {
        return $this === self::ENFANT;
    }

    public function getMinAge(): ?int
    {
        return match ($this) {
            self::ENFANT => 0,
            default      => null,
        };
    }

    public function getMaxAge(): ?int
    {
        return match ($this) {
            self::ENFANT => 11,
            default      => null,
        };
    }

    public function isEligibleForAge(int $age): bool
    {
        $minAge = $this->getMinAge();
        $maxAge = $this->getMaxAge();

        if ($minAge !== null && $age < $minAge) {
            return false;
        }

        if ($maxAge !== null && $age > $maxAge) {
            return false;
        }

        return true;
    }

    public function getDiscountPercentage(): int
    {
        return match ($this) {
            self::NORMAL => 0,
            self::REDUIT => 25, // 25% de réduction
            self::ENFANT => 35, // 35% de réduction
        };
    }

    /**
     * Calcule le prix par défaut à partir d'un prix de base
     */
    public function calculateDefaultPrice(float $basePriceEuros = 12.50): string
    {
        $discountPercentage = $this->getDiscountPercentage();
        $price              = $basePriceEuros * (1 - $discountPercentage / 100);

        return number_format($price, 2, '.', '');
    }

    public function getColorClass(): string
    {
        return match ($this) {
            self::NORMAL => 'text-gray-600',
            self::REDUIT => 'text-blue-600',
            self::ENFANT => 'text-green-600',
        };
    }

    /**
     * Prix par défaut temporaire - à terme depuis config/BDD/cinéma
     */
    public function getDefaultPrice(): string
    {
        return $this->calculateDefaultPrice();
    }
}
