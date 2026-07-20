<?php

declare(strict_types=1);

namespace App\Application\Contracts;

use LogicException;

/**
 * Représente un résultat de succès
 *
 * Contient la valeur de retour d'une opération réussie
 */
final readonly class Success extends Result
{
    public function __construct(
        private mixed $value
    ) {}

    public function isSuccess(): bool
    {
        return true;
    }

    public function getValue(): mixed
    {
        return $this->value;
    }

    public function getError(): mixed
    {
        throw new LogicException('Cannot get error from a Success result');
    }

    public function getErrorMessage(): ?string
    {
        throw new LogicException('Cannot get error message from a Success result');
    }
}
