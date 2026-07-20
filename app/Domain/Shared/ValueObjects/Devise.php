<?php

declare(strict_types=1);

namespace App\Domain\Shared\ValueObjects;

use InvalidArgumentException;
use App\Domain\Shared\Enums\CodePays;
use Respect\Validation\Validator as v;

final readonly class Devise
{
    public function __construct(
        public readonly string $code
    ) {
        $this->enforceInvariant();
    }

    public function __toString(): string
    {
        return $this->code;
    }

    public static function fromString(string $code): self
    {
        return new self(strtoupper(trim($code)));
    }

    public static function fromCountry(CodePays $pays): self
    {
        // Default currency mapping based on country
        $currency = match ($pays) {
            CodePays::France, CodePays::Belgique, CodePays::Luxembourg => 'EUR',
            CodePays::Suisse => 'CHF',
            default          => 'EUR'
        };

        return new self($currency);
    }

    public static function tryFromString(?string $code): ?self
    {
        if ($code === null) {
            return null;
        }

        try {
            return self::fromString($code);
        } catch (InvalidArgumentException) {
            return null;
        }
    }

    /**
     * @return array<Devise>
     */
    public static function getSupportedCurrencies(): array
    {
        return array_map(
            fn (string $code) => new self($code),
            self::getDevisesSupportees()
        );
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getSymbol(): string
    {
        return match ($this->code) {
            'EUR'   => '€',
            'CHF'   => 'CHF',
            'CAD'   => 'C$',
            'USD'   => '$',
            default => $this->code,
        };
    }

    public function getName(): string
    {
        return match ($this->code) {
            'EUR'   => 'Euro',
            'CHF'   => 'Franc Suisse',
            'CAD'   => 'Dollar Canadien',
            'USD'   => 'Dollar Américain',
            default => $this->code,
        };
    }

    public function getDecimalPlaces(): int
    {
        return match ($this->code) {
            'EUR', 'CHF', 'CAD', 'USD' => 2,
            default => 2,
        };
    }

    public function isEuro(): bool
    {
        return $this->code === 'EUR';
    }

    public function isMajorCurrency(): bool
    {
        return in_array($this->code, ['EUR', 'USD', 'CHF']);
    }

    public function formatAmount(int $centimes): string
    {
        $amount   = $centimes / 100;
        $decimals = $this->getDecimalPlaces();

        return match ($this->code) {
            'EUR'   => number_format($amount, $decimals, ',', ' ') . ' €',
            'CHF'   => 'CHF ' . number_format($amount, $decimals, '.', '\''),
            'CAD'   => 'C$ ' . number_format($amount, $decimals, '.', ','),
            'USD'   => '$' . number_format($amount, $decimals, '.', ','),
            default => number_format($amount, $decimals) . ' ' . $this->code,
        };
    }

    /**
     * @return array<CodePays>
     */
    public function getCountries(): array
    {
        return match ($this->code) {
            'EUR'   => [CodePays::France, CodePays::Belgique, CodePays::Luxembourg],
            'CHF'   => [CodePays::Suisse],
            'USD'   => [],
            default => [],
        };
    }

    public function isUsedInCountry(CodePays $pays): bool
    {
        return in_array($pays, $this->getCountries());
    }

    /**
     * @return array{code: string, symbol: string, name: string, decimal_places: int, is_major: bool}
     */
    public function getExchangeInfo(): array
    {
        return [
            'code'           => $this->code,
            'symbol'         => $this->getSymbol(),
            'name'           => $this->getName(),
            'decimal_places' => $this->getDecimalPlaces(),
            'is_major'       => $this->isMajorCurrency(),
        ];
    }

    public function equals(Devise $other): bool
    {
        return $this->code === $other->code;
    }

    /**
     * @return array<string>
     */
    private static function getDevisesSupportees(): array
    {
        return \App\Domain\Shared\Enums\DeviseEnum::values();
    }

    private function enforceInvariant(): void
    {
        $this->validateLength();
        $this->validateSupported();
    }

    private function validateLength(): void
    {
        if (!v::length(3, 3)->validate($this->code)) {
            throw new InvalidArgumentException('Le code devise doit faire exactement 3 caractères');
        }
    }

    private function validateSupported(): void
    {
        $supportedCurrencies = self::getDevisesSupportees();
        if (!in_array($this->code, $supportedCurrencies)) {
            throw new InvalidArgumentException(
                "Devise non supportée: {$this->code}. Devises supportées: " .
                implode(', ', $supportedCurrencies)
            );
        }
    }
}
