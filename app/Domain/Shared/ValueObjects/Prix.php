<?php

declare(strict_types=1);

namespace App\Domain\Shared\ValueObjects;

use Money\Money;
use InvalidArgumentException;

final readonly class Prix
{
    public function __construct(
        public readonly Money $montantHT,
        public readonly TauxTva $tauxTva
    ) {
        $this->enforceInvariant();
    }

    public function __toString(): string
    {
        return $this->formatTTC();
    }

    public static function fromHT(Money $montantHT, TauxTva $tauxTva): self
    {
        return new self($montantHT, $tauxTva);
    }

    public static function fromTTC(Money $montantTTC, TauxTva $tauxTva): self
    {
        $montantHT = MoneyHelper::removeTva($montantTTC, $tauxTva);

        return new self($montantHT, $tauxTva);
    }

    public static function tryFromHT(?Money $montantHT, ?TauxTva $tauxTva): ?self
    {
        if ($montantHT === null || $tauxTva === null) {
            return null;
        }

        try {
            return self::fromHT($montantHT, $tauxTva);
        } catch (InvalidArgumentException) {
            return null;
        }
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $montantHT = MoneyHelper::fromArray($data['montant_ht']);
        $tauxTva   = TauxTva::fromArray($data['taux_tva']);

        return new self($montantHT, $tauxTva);
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function tryFromArray(array $data): ?self
    {
        try {
            return self::fromArray($data);
        } catch (InvalidArgumentException) {
            return null;
        }
    }

    public function getMontantHT(): Money
    {
        return $this->montantHT;
    }

    public function getTauxTva(): TauxTva
    {
        return $this->tauxTva;
    }

    public function getMontantTva(): Money
    {
        return MoneyHelper::calculateTva($this->montantHT, $this->tauxTva);
    }

    public function getMontantTTC(): Money
    {
        return MoneyHelper::addTva($this->montantHT, $this->tauxTva);
    }

    public function isZero(): bool
    {
        return MoneyHelper::isZero($this->montantHT);
    }

    public function isPositive(): bool
    {
        return MoneyHelper::isPositive($this->montantHT);
    }

    public function formatHT(): string
    {
        return MoneyHelper::formatForCountry($this->montantHT, $this->getCountryFromCurrency());
    }

    public function formatTTC(): string
    {
        return MoneyHelper::formatForCountry($this->getMontantTTC(), $this->getCountryFromCurrency());
    }

    public function formatTva(): string
    {
        return MoneyHelper::formatForCountry($this->getMontantTva(), $this->getCountryFromCurrency());
    }

    /**
     * @return array{ht: string, tva: string, ttc: string, taux_tva: string}
     */
    public function getDetailFormatte(): array
    {
        return [
            'ht'       => $this->formatHT(),
            'tva'      => $this->formatTva(),
            'ttc'      => $this->formatTTC(),
            'taux_tva' => $this->tauxTva->formatForDisplay(),
        ];
    }

    public function applquerRemise(float $pourcentageRemise): self
    {
        $nouveauMontantHT = MoneyHelper::applyDiscount($this->montantHT, $pourcentageRemise);

        return new self($nouveauMontantHT, $this->tauxTva);
    }

    public function changerTva(TauxTva $nouveauTaux): self
    {
        return new self($this->montantHT, $nouveauTaux);
    }

    public function add(Prix $autrePrix): self
    {
        if (!$this->hasSameCurrency($autrePrix)) {
            throw new InvalidArgumentException('Cannot add prices with different currencies');
        }

        if (!$this->tauxTva->equals($autrePrix->tauxTva)) {
            throw new InvalidArgumentException('Cannot add prices with different VAT rates');
        }

        $nouveauMontantHT = $this->montantHT->add($autrePrix->montantHT);

        return new self($nouveauMontantHT, $this->tauxTva);
    }

    public function subtract(Prix $autrePrix): self
    {
        if (!$this->hasSameCurrency($autrePrix)) {
            throw new InvalidArgumentException('Cannot subtract prices with different currencies');
        }

        if (!$this->tauxTva->equals($autrePrix->tauxTva)) {
            throw new InvalidArgumentException('Cannot subtract prices with different VAT rates');
        }

        $nouveauMontantHT = $this->montantHT->subtract($autrePrix->montantHT);

        return new self($nouveauMontantHT, $this->tauxTva);
    }

    public function multiply(int $multiplicateur): self
    {
        $nouveauMontantHT = $this->montantHT->multiply($multiplicateur);

        return new self($nouveauMontantHT, $this->tauxTva);
    }

    public function equals(Prix $autrePrix): bool
    {
        return MoneyHelper::equals($this->montantHT, $autrePrix->montantHT) &&
               $this->tauxTva->equals($autrePrix->tauxTva);
    }

    public function compareTo(Prix $autrePrix): int
    {
        if (!$this->hasSameCurrency($autrePrix)) {
            throw new InvalidArgumentException('Cannot compare prices with different currencies');
        }

        return MoneyHelper::compare($this->montantHT, $autrePrix->montantHT);
    }

    public function isGreaterThan(Prix $autrePrix): bool
    {
        return $this->compareTo($autrePrix) > 0;
    }

    public function isLessThan(Prix $autrePrix): bool
    {
        return $this->compareTo($autrePrix) < 0;
    }

    public function hasSameCurrency(Prix $autrePrix): bool
    {
        return $this->montantHT->getCurrency()->equals($autrePrix->montantHT->getCurrency());
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'montant_ht'  => MoneyHelper::toArray($this->montantHT),
            'taux_tva'    => $this->tauxTva->toArray(),
            'montant_tva' => MoneyHelper::toArray($this->getMontantTva()),
            'montant_ttc' => MoneyHelper::toArray($this->getMontantTTC()),
        ];
    }

    private function getCountryFromCurrency(): \App\Domain\Shared\Enums\CodePays
    {
        $currencyCode = $this->montantHT->getCurrency()->getCode();

        return match ($currencyCode) {
            'EUR'   => \App\Domain\Shared\Enums\CodePays::France,
            'CHF'   => \App\Domain\Shared\Enums\CodePays::Suisse,
            'CAD'   => \App\Domain\Shared\Enums\CodePays::France, // Canada not in enum
            default => \App\Domain\Shared\Enums\CodePays::France,
        };
    }

    private function enforceInvariant(): void
    {
        $this->validateCurrencyCompatibility();
        $this->validatePositiveAmount();
    }

    private function validateCurrencyCompatibility(): void
    {
        // Vérification que la devise est supportée
        $supportedCurrencies = ['EUR', 'CHF', 'CAD', 'USD'];
        $currency            = $this->montantHT->getCurrency()->getCode();

        if (!in_array($currency, $supportedCurrencies)) {
            throw new InvalidArgumentException("Devise non supportée: {$currency}");
        }
    }

    private function validatePositiveAmount(): void
    {
        if (!MoneyHelper::isPositive($this->montantHT) && !MoneyHelper::isZero($this->montantHT)) {
            throw new InvalidArgumentException('Le montant HT doit être positif ou zéro');
        }
    }
}
