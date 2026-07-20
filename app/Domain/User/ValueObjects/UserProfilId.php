<?php

declare(strict_types=1);

namespace App\Domain\User\ValueObjects;

use InvalidArgumentException;
use App\Domain\Shared\ValueObjects\Identity;

/**
 * Identifiant unique pour les entités UserProfil
 */
final readonly class UserProfilId extends Identity
{
    /**
     * Génère un nouvel ID UserProfil
     */
    public static function generate(): self
    {
        return new self(parent::generateUuid());
    }

    /**
     * Crée un UserProfilId depuis une chaîne existante
     */
    public static function fromString(string $uuid): self
    {
        return new self($uuid);
    }

    /**
     * Crée un UserProfilId depuis une chaîne (version safe)
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
