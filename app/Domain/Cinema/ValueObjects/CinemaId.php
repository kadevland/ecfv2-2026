<?php

declare(strict_types=1);

namespace App\Domain\Cinema\ValueObjects;

use InvalidArgumentException;
use App\Domain\Shared\ValueObjects\Identity;

/**
 * Identifiant unique pour les entités Cinema
 */
final readonly class CinemaId extends Identity
{
    /**
     * Génère un nouvel ID Cinema
     */
    public static function generate(): self
    {
        return new self(parent::generateUuid());
    }

    /**
     * Crée un CinemaId depuis une chaîne existante
     */
    public static function fromString(string $uuid): self
    {
        return new self($uuid);
    }

    /**
     * Crée un CinemaId depuis une chaîne (version safe)
     */
    public static function tryFromString(?string $uuid): ?self
    {
        try {
            return self::fromString($uuid);
        } catch (InvalidArgumentException) {
            return null;
        }
    }
}
