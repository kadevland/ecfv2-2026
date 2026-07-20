<?php

declare(strict_types=1);

namespace App\Domain\Employees\ValueObjects;

use InvalidArgumentException;
use App\Domain\Shared\ValueObjects\Identity;

/**
 * Identifiant unique pour les entités Contrat
 */
final readonly class ContratId extends Identity
{
    public static function generate(): self
    {
        return new self(parent::generateUuid());
    }

    public static function fromString(string $uuid): self
    {
        return new self($uuid);
    }

    public static function tryFromString(?string $uuid): ?self
    {
        try {
            return self::fromString($uuid);
        } catch (InvalidArgumentException) {
            return null;
        }
    }
}
