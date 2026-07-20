<?php

declare(strict_types=1);

namespace App\Domain\Shared\ValueObjects;

use Ramsey\Uuid\Uuid;
use InvalidArgumentException;

/**
 * Identity Value Object - UUID abstrait pour toutes les entités
 */
abstract readonly class Identity
{
    public function __construct(public string $value)
    {
        $this->enforceInvariant();
    }

    /**
     * Représentation string pour debugging
     */
    public function __toString(): string
    {
        return $this->value;
    }

    /**
     * Permet l'égalité entre instances
     */
    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }

    /**
     * Génère une nouvelle identité unique avec timestamp (UUID v7) - méthode protégée
     */
    protected static function generateUuid(): string
    {
        return Uuid::uuid7()->toString();
    }

    /**
     * Vérifie les invariants du Value Object
     * Utilise Ramsey UUID pour validation native des UUID v7
     */
    private function enforceInvariant(): void
    {
        if (empty($this->value) || !Uuid::isValid($this->value)) {
            throw new InvalidArgumentException(
                "Invalid UUID format: {$this->value}"
            );
        }
    }
}
