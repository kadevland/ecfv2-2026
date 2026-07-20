<?php

declare(strict_types=1);

namespace App\Application\Contracts;

use LogicException;

/**
 * Représente un résultat d'erreur
 *
 * Contient l'erreur et un message optionnel d'une opération échouée
 */
final readonly class Error extends Result
{
    public function __construct(
        private mixed $error,
        private ?string $message = null
    ) {}

    /**
     * Crée une erreur avec un message automatique si l'erreur est un enum
     */
    public static function fromEnum(mixed $error): self
    {
        $message = null;

        // Si l'erreur est un enum avec une méthode message(), l'utiliser
        if (is_object($error) && method_exists($error, 'message')) {
            $message = $error->message();
        }

        return new self($error, $message);
    }

    public function isSuccess(): bool
    {
        return false;
    }

    public function getValue(): mixed
    {
        throw new LogicException('Cannot get value from an Error result');
    }

    public function getError(): mixed
    {
        return $this->error;
    }

    public function getErrorMessage(): ?string
    {
        return $this->message;
    }
}
