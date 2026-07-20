<?php

declare(strict_types=1);

namespace App\Domain\Shared\ValueObjects;

use Money\Money;
use Money\Currency;
use InvalidArgumentException;
use App\Domain\Shared\Enums\CodePays;

/**
 * Helper class pour MoneyPHP avec business logic spécifique Cinéphoria
 *
 * Utilise MoneyPHP (https://www.moneyphp.org/) comme base
 * et ajoute des méthodes business pour TVA, pays, etc.
 */
final class MoneyHelper
{
    /**
     * Crée un Money depuis centimes et devise
     */
    public static function fromCentimes(int $centimes, string $deviseCode): Money
    {
        if ($centimes < 0) {
            throw new InvalidArgumentException('Le montant ne peut pas être négatif');
        }

        return new Money($centimes, new Currency($deviseCode));
    }

    /**
     * Crée un Money depuis amount float et devise
     */
    public static function fromAmount(float $amount, string $deviseCode): Money
    {
        if ($amount < 0) {
            throw new InvalidArgumentException('Le montant ne peut pas être négatif');
        }

        $centimes = (int) round($amount * 100);

        return new Money($centimes, new Currency($deviseCode));
    }

    /**
     * Crée un Money EUR depuis euros
     */
    public static function fromEuros(float $euros): Money
    {
        return self::fromAmount($euros, 'EUR');
    }

    /**
     * Crée un Money depuis devise du pays
     */
    public static function fromCountryAmount(float $amount, CodePays $pays): Money
    {
        $devise = Devise::fromCountry($pays);

        return self::fromAmount($amount, $devise->getCode());
    }

    /**
     * Money zéro pour une devise
     */
    public static function zero(string $deviseCode): Money
    {
        return new Money(0, new Currency($deviseCode));
    }

    /**
     * Money zéro EUR
     */
    public static function zeroEur(): Money
    {
        return self::zero('EUR');
    }

    /**
     * Calcule la TVA sur un montant HT
     */
    public static function calculateTva(Money $montantHT, TauxTva $tauxTva): Money
    {
        $decimal     = $tauxTva->getDecimal();
        $centimesTva = (int) round($montantHT->getAmount() * $decimal);

        return new Money($centimesTva, $montantHT->getCurrency());
    }

    /**
     * Ajoute la TVA à un montant HT pour obtenir TTC
     */
    public static function addTva(Money $montantHT, TauxTva $tauxTva): Money
    {
        $tva = self::calculateTva($montantHT, $tauxTva);

        return $montantHT->add($tva);
    }

    /**
     * Retire la TVA d'un montant TTC pour obtenir HT
     */
    public static function removeTva(Money $montantTTC, TauxTva $tauxTva): Money
    {
        $divisor    = 1 + $tauxTva->getDecimal();
        $centimesHT = (int) round($montantTTC->getAmount() / $divisor);

        return new Money($centimesHT, $montantTTC->getCurrency());
    }

    /**
     * Formate un Money selon les conventions locales
     */
    public static function formatForCountry(Money $money, CodePays $pays): string
    {
        $amount   = $money->getAmount() / 100;
        $currency = $money->getCurrency()->getCode();

        return match ($currency) {
            'EUR'   => number_format($amount, 2, ',', ' ') . ' €',
            'CHF'   => 'CHF ' . number_format($amount, 2, '.', '\''),
            'CAD'   => 'C$ ' . number_format($amount, 2, '.', ','),
            'USD'   => '$' . number_format($amount, 2, '.', ','),
            default => number_format($amount, 2) . ' ' . $currency,
        };
    }

    /**
     * Vérifie si une devise est compatible avec un pays
     */
    public static function isDeviseCompatibleWithCountry(string $deviseCode, CodePays $pays): bool
    {
        $devise = Devise::fromCountry($pays);

        return $deviseCode === $devise->getCode();
    }

    /**
     * Convertit un Money en array pour stockage JSONB
     *
     * @return array{centimes: int, devise: string, amount: float}
     */
    public static function toArray(Money $money): array
    {
        return [
            'centimes' => (int) $money->getAmount(),
            'devise'   => $money->getCurrency()->getCode(),
            'amount'   => (float) ($money->getAmount() / 100),
        ];
    }

    /**
     * Recrée un Money depuis array JSONB
     *
     * @param array{centimes: int, devise: string} $data
     */
    public static function fromArray(array $data): Money
    {
        return new Money(
            $data['centimes'],
            new Currency($data['devise'])
        );
    }

    /**
     * Vérifie si un Money est zero
     */
    public static function isZero(Money $money): bool
    {
        return $money->isZero();
    }

    /**
     * Vérifie si un Money est positif
     */
    public static function isPositive(Money $money): bool
    {
        return $money->isPositive();
    }

    /**
     * Compare deux Money (même devise requise)
     */
    public static function compare(Money $money1, Money $money2): int
    {
        return $money1->compare($money2);
    }

    /**
     * Vérifie égalité entre deux Money
     */
    public static function equals(Money $money1, Money $money2): bool
    {
        return $money1->equals($money2);
    }

    /**
     * Allocation proportionnelle d'un montant
     *
     * @param array<int> $ratios
     * @return array<Money>
     */
    public static function allocate(Money $money, array $ratios): array
    {
        return $money->allocate($ratios);
    }

    /**
     * Minimum entre deux Money
     */
    public static function min(Money $money1, Money $money2): Money
    {
        return $money1->lessThan($money2) ? $money1 : $money2;
    }

    /**
     * Maximum entre deux Money
     */
    public static function max(Money $money1, Money $money2): Money
    {
        return $money1->greaterThan($money2) ? $money1 : $money2;
    }

    /**
     * Somme d'un array de Money (même devise)
     *
     * @param array<Money> $moneys
     */
    public static function sum(array $moneys): Money
    {
        if (empty($moneys)) {
            throw new InvalidArgumentException('Cannot sum empty array');
        }

        $first  = reset($moneys);
        $result = self::zero($first->getCurrency()->getCode());

        foreach ($moneys as $money) {
            $result = $result->add($money);
        }

        return $result;
    }

    /**
     * Calcule une remise en pourcentage
     */
    public static function applyDiscount(Money $money, float $discountPercent): Money
    {
        if ($discountPercent < 0 || $discountPercent > 100) {
            throw new InvalidArgumentException('Discount percent must be between 0 and 100');
        }

        $factor    = (100 - $discountPercent) / 100;
        $newAmount = (int) round($money->getAmount() * $factor);

        return new Money($newAmount, $money->getCurrency());
    }

    /**
     * Factory safe qui retourne null en cas d'erreur
     */
    public static function tryFromCentimes(?int $centimes, ?string $deviseCode): ?Money
    {
        if ($centimes === null || $deviseCode === null) {
            return null;
        }

        try {
            return self::fromCentimes($centimes, $deviseCode);
        } catch (InvalidArgumentException) {
            return null;
        }
    }

    /**
     * Factory safe qui retourne null en cas d'erreur
     */
    public static function tryFromAmount(?float $amount, ?string $deviseCode): ?Money
    {
        if ($amount === null || $deviseCode === null) {
            return null;
        }

        try {
            return self::fromAmount($amount, $deviseCode);
        } catch (InvalidArgumentException) {
            return null;
        }
    }
}
