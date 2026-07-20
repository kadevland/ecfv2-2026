<?php

declare(strict_types=1);

namespace App\Domain\User\ValueObjects;

use App\Domain\Shared\ValueObjects\Identity;

/**
 * Identifiant unique pour les entités User
 */
final readonly class UserId extends Identity
{
    /**
     * Génère un nouvel ID User
     */
    public static function generate(): self
    {
        return new self(parent::generateUuid());
    }

    /**
     * Crée un UserId depuis une chaîne existante
     */
    public static function fromString(string $uuid): self
    {
        return new self($uuid);
    }
}
