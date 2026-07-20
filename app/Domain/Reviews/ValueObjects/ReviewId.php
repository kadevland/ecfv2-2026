<?php

declare(strict_types=1);

namespace App\Domain\Reviews\ValueObjects;

use App\Domain\Shared\ValueObjects\Identity;

/**
 * Identifiant unique pour les entités Review
 */
final readonly class ReviewId extends Identity
{
    /**
     * Génère un nouvel ID Review
     */
    public static function generate(): self
    {
        return new self(parent::generateUuid());
    }
}
