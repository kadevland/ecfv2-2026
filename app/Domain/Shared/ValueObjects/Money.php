<?php

declare(strict_types=1);

namespace App\Domain\Shared\ValueObjects;

use JsonSerializable;
use InvalidArgumentException;

/**
 * Value Object pour représenter une somme d'argent
 */
final readonly class Money implements JsonSerializable
{
    public function __construct(
        public int $amountInCentimes,
        public Devise $devise,
    ) {
        if ($this->amountInCentimes < 0) {
            throw new InvalidArgumentException('Amount cannot be negative');
        }
    }

    public function __toString(): string
    {
        return $this->format();
    }

    /**
     * Créer un objet Money à partir d'un montant en euros
     */
    public static function fromEuros(float $amount): self
    {
        return new self(
            amountInCentimes: (int) round($amount * 100),
            devise: Devise::fromString('EUR')
        );
    }

    /**
     * Créer un objet Money à partir d'un montant en centimes
     */
    public static function fromCentimes(int $centimes, ?Devise $devise = null): self
    {
        return new self(
            amountInCentimes: $centimes,
            devise: $devise ?? Devise::fromString('EUR')
        );
    }

    /**
     * Obtenir le montant en euros (float)
     */
    public function toFloat(): float
    {
        return $this->amountInCentimes / 100;
    }

    /**
     * Obtenir le montant formaté
     */
    public function format(): string
    {
        return number_format($this->toFloat(), 2, ',', ' ') . ' ' . $this->devise->code;
    }

    /**
     * Ajouter un montant
     */
    public function add(Money $other): self
    {
        if (!$this->devise->equals($other->devise)) {
            throw new InvalidArgumentException('Cannot add money with different currencies');
        }

        return new self(
            amountInCentimes: $this->amountInCentimes + $other->amountInCentimes,
            devise: $this->devise
        );
    }

    /**
     * Soustraire un montant
     */
    public function subtract(Money $other): self
    {
        if (!$this->devise->equals($other->devise)) {
            throw new InvalidArgumentException('Cannot subtract money with different currencies');
        }

        return new self(
            amountInCentimes: $this->amountInCentimes - $other->amountInCentimes,
            devise: $this->devise
        );
    }

    /**
     * Multiplier par un facteur
     */
    public function multiply(float $factor): self
    {
        return new self(
            amountInCentimes: (int) round($this->amountInCentimes * $factor),
            devise: $this->devise
        );
    }

    /**
     * Vérifier l'égalité avec un autre montant
     */
    public function equals(Money $other): bool
    {
        return $this->amountInCentimes === $other->amountInCentimes
            && $this->devise->equals($other->devise);
    }

    /**
     * Vérifier si le montant est positif
     */
    public function isPositive(): bool
    {
        return $this->amountInCentimes > 0;
    }

    /**
     * Vérifier si le montant est zéro
     */
    public function isZero(): bool
    {
        return $this->amountInCentimes === 0;
    }

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return [
            'amount_centimes' => $this->amountInCentimes,
            'amount_euros'    => $this->toFloat(),
            'devise'          => $this->devise->code,
            'formatted'       => $this->format(),
        ];
    }
}
