<?php

declare(strict_types=1);

namespace App\Domain\Shared\ValueObjects;

use InvalidArgumentException;
use Respect\Validation\Validator as v;

final readonly class Prenom
{
    private const MAX_LENGTH = 100;

    private const MIN_LENGTH = 1;

    public function __construct(
        public readonly string $value
    ) {
        $this->enforceInvariant();
    }

    public function __toString(): string
    {
        return $this->value;
    }

    public static function fromString(string $prenom): self
    {
        return new self(trim($prenom));
    }

    public static function tryFromString(?string $prenom): ?self
    {
        if ($prenom === null) {
            return null;
        }

        try {
            return self::fromString($prenom);
        } catch (InvalidArgumentException) {
            return null;
        }
    }

    public function toString(): string
    {
        return $this->value;
    }

    public function toLowerCase(): string
    {
        return mb_strtolower($this->value, 'UTF-8');
    }

    public function toUpperCase(): string
    {
        return mb_strtoupper($this->value, 'UTF-8');
    }

    public function capitalize(): string
    {
        return mb_convert_case($this->value, MB_CASE_TITLE, 'UTF-8');
    }

    /**
     * @return array<string>
     */
    public function getWords(): array
    {
        return array_filter(
            explode(' ', $this->value),
            fn (string $word) => !empty(trim($word))
        );
    }

    public function getWordCount(): int
    {
        return count($this->getWords());
    }

    public function getFirstName(): string
    {
        $words = $this->getWords();

        return $words[0] ?? '';
    }

    public function getUsualName(): string
    {
        return $this->getFirstName();
    }

    /**
     * @return array<string>
     */
    public function getAllNames(): array
    {
        return $this->getWords();
    }

    public function hasMultipleNames(): bool
    {
        return $this->getWordCount() > 1;
    }

    public function getSecondName(): ?string
    {
        $words = $this->getWords();

        return $words[1] ?? null;
    }

    public function getInitial(): string
    {
        return mb_strtoupper(mb_substr($this->getFirstName(), 0, 1, 'UTF-8'), 'UTF-8');
    }

    public function getAllInitials(): string
    {
        $words    = $this->getWords();
        $initials = '';

        foreach ($words as $word) {
            $initials .= mb_strtoupper(mb_substr(trim($word), 0, 1, 'UTF-8'), 'UTF-8');
        }

        return $initials;
    }

    public function formatForDisplay(): string
    {
        return $this->capitalize();
    }

    public function formatShort(): string
    {
        $words = $this->getWords();
        if (count($words) <= 1) {
            return $this->getFirstName();
        }

        $result = $this->getFirstName();
        for ($i = 1; $i < count($words); $i++) {
            $result .= ' ' . mb_strtoupper(mb_substr($words[$i], 0, 1, 'UTF-8'), 'UTF-8') . '.';
        }

        return $result;
    }

    public function isFrenchVariant(): bool
    {
        $frenchVariants = ['Jean-', 'Marie-', 'Pierre-', 'Anne-'];

        foreach ($frenchVariants as $prefix) {
            if ($this->startsWith($prefix)) {
                return true;
            }
        }

        return false;
    }

    public function isComposed(): bool
    {
        return $this->getWordCount() > 1 ||
               str_contains($this->value, '-') ||
               str_contains($this->value, '\'');
    }

    public function containsSpecialCharacters(): bool
    {
        return preg_match('/[\-\'\.]/u', $this->value) === 1;
    }

    public function length(): int
    {
        return mb_strlen($this->value, 'UTF-8');
    }

    public function equals(Prenom $other): bool
    {
        return $this->toLowerCase() === $other->toLowerCase();
    }

    public function startsWith(string $prefix): bool
    {
        return mb_stripos($this->value, $prefix, 0, 'UTF-8') === 0;
    }

    public function contains(string $substring): bool
    {
        return mb_stripos($this->value, $substring, 0, 'UTF-8') !== false;
    }

    private function enforceInvariant(): void
    {
        $this->validateNotEmpty();
        $this->validateLength();
        $this->validateCharacters();
        $this->validateNotOnlySpecialChars();
        $this->validateNoConsecutiveSpecialChars();
        $this->validateStartEndChars();
    }

    private function validateNotEmpty(): void
    {
        if (!v::notEmpty()->validate(trim($this->value))) {
            throw new InvalidArgumentException('Le prénom ne peut pas être vide');
        }
    }

    private function validateLength(): void
    {
        if (!v::length(self::MIN_LENGTH, self::MAX_LENGTH)->validate($this->value)) {
            throw new InvalidArgumentException(
                'Le prénom doit contenir entre ' . self::MIN_LENGTH . ' et ' . self::MAX_LENGTH . ' caractères'
            );
        }
    }

    private function validateCharacters(): void
    {
        if (!v::regex('/^[a-zA-ZÀ-ÿ\s\-\'\.]+$/u')->validate($this->value)) {
            throw new InvalidArgumentException(
                'Le prénom contient des caractères invalides. Seuls les lettres, espaces, tirets, apostrophes et points sont autorisés'
            );
        }
    }

    private function validateNotOnlySpecialChars(): void
    {
        if (!v::regex('/[a-zA-ZÀ-ÿ]/u')->validate($this->value)) {
            throw new InvalidArgumentException('Le prénom doit contenir au moins une lettre');
        }
    }

    private function validateNoConsecutiveSpecialChars(): void
    {
        if (v::regex('/[\s\-\'\.]{2,}/u')->validate($this->value)) {
            throw new InvalidArgumentException('Le prénom ne peut pas contenir de caractères spéciaux consécutifs');
        }
    }

    private function validateStartEndChars(): void
    {
        if (v::regex('/^[\s\-\'\.]/u')->validate($this->value) || v::regex('/[\s\-\'\.]$/u')->validate($this->value)) {
            throw new InvalidArgumentException('Le prénom ne peut pas commencer ou finir par un caractère spécial');
        }
    }
}
