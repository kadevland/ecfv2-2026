<?php

declare(strict_types=1);

namespace App\Application\Shared\Exceptions;

use Exception;

final class EntityNotFoundException extends Exception
{
    public function __construct(string $entityType, string $identifier)
    {
        parent::__construct("Entity '{$entityType}' with identifier '{$identifier}' not found");
    }

    public static function forId(string $entityType, string $id): self
    {
        return new self($entityType, $id);
    }

    public static function forUuid(string $entityType, string $uuid): self
    {
        return new self($entityType, $uuid);
    }
}
