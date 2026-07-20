<?php

declare(strict_types=1);

namespace App\Infrastructure\Casts;

use App\Domain\Cinema\ValueObjects\CinemaId;
use App\Domain\Shared\ValueObjects\Identity;

/**
 * Cast automatique Laravel pour CinemaId Value Object
 *
 * @extends AbstractIdentityCast<CinemaId>
 */
final class CinemaIdCast extends AbstractIdentityCast
{
    protected function fromString(string $uuid): Identity
    {
        return CinemaId::fromString($uuid);
    }
}
