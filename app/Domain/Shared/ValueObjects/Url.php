<?php

declare(strict_types=1);

namespace App\Domain\Shared\ValueObjects;

use InvalidArgumentException;
use Respect\Validation\Validator as v;

/**
 * Value Object pour URLs web
 * Assure la validité du format URL
 */
final readonly class Url
{
    private const MAX_LENGTH = 2048; // RFC 3986 limite pratique

    public function __construct(
        public readonly string $value
    ) {
        $this->enforceInvariant();
    }

    public function __toString(): string
    {
        return $this->value;
    }

    public static function fromString(string $url): self
    {
        return new self(trim($url));
    }

    public static function tryFromString(?string $url): ?self
    {
        if ($url === null) {
            return null;
        }

        try {
            return self::fromString($url);
        } catch (InvalidArgumentException) {
            return null;
        }
    }

    public function isSecure(): bool
    {
        return str_starts_with($this->value, 'https://');
    }

    public function getDomain(): string
    {
        $parsed = parse_url($this->value);

        return $parsed['host'] ?? '';
    }

    public function getScheme(): string
    {
        $parsed = parse_url($this->value);

        return $parsed['scheme'] ?? '';
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }

    public function toString(): string
    {
        return $this->value;
    }

    private function enforceInvariant(): void
    {
        $this->validateNotEmpty();
        $this->validateFormat();
        $this->validateProtocol();
        $this->validateLength();
    }

    private function validateNotEmpty(): void
    {
        if (!v::notEmpty()->validate($this->value)) {
            throw new InvalidArgumentException('L\'URL ne peut pas être vide');
        }
    }

    private function validateFormat(): void
    {
        if (!v::url()->validate($this->value)) {
            throw new InvalidArgumentException("'{$this->value}' n'est pas une URL valide");
        }
    }

    private function validateProtocol(): void
    {
        if (!v::startsWith('http://')->validate($this->value) &&
            !v::startsWith('https://')->validate($this->value)) {
            throw new InvalidArgumentException("L'URL doit commencer par http:// ou https://: {$this->value}");
        }
    }

    private function validateLength(): void
    {
        if (!v::length(null, self::MAX_LENGTH)->validate($this->value)) {
            throw new InvalidArgumentException('L\'URL ne peut pas dépasser ' . self::MAX_LENGTH . ' caractères');
        }
    }
}
