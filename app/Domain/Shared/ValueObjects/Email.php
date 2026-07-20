<?php

declare(strict_types=1);

namespace App\Domain\Shared\ValueObjects;

use InvalidArgumentException;
use Respect\Validation\Validator as v;

final readonly class Email
{
    private const MAX_LENGTH = 320; // RFC 5322 limite

    public function __construct(
        public readonly string $value
    ) {
        $this->enforceInvariant();
    }

    public function __toString(): string
    {
        return $this->value;
    }

    public static function fromString(string $email): self
    {
        return new self(mb_strtolower(trim($email)));
    }

    public static function tryFromString(?string $email): ?self
    {
        if ($email === null) {
            return null;
        }

        try {
            return self::fromString($email);
        } catch (InvalidArgumentException) {
            return null;
        }
    }

    public function getDomain(): string
    {
        return substr($this->value, strpos($this->value, '@') + 1);
    }

    public function getLocalPart(): string
    {
        return substr($this->value, 0, strpos($this->value, '@'));
    }

    public function equals(Email $other): bool
    {
        return strtolower($this->value) === strtolower($other->value);
    }

    public function toString(): string
    {
        return $this->value;
    }

    private function enforceInvariant(): void
    {
        $this->validateFormat();
        $this->validateLength();
    }

    private function validateFormat(): void
    {
        if (!v::email()->validate($this->value)) {
            throw new InvalidArgumentException("'{$this->value}' n'est pas une adresse email valide");
        }
    }

    private function validateLength(): void
    {
        if (!v::length(null, self::MAX_LENGTH)->validate($this->value)) {
            throw new InvalidArgumentException('L\'adresse email ne peut pas dépasser ' . self::MAX_LENGTH . ' caractères');
        }
    }
}
