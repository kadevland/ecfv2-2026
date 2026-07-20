<?php

declare(strict_types=1);

namespace App\Domain\Shared\ValueObjects;

use InvalidArgumentException;
use App\Domain\Shared\Enums\CodePays;
use Respect\Validation\Validator as v;

final readonly class CodePostal
{
    public function __construct(
        public readonly string $value,
        public readonly CodePays $pays
    ) {
        $this->enforceInvariant();
    }

    public function __toString(): string
    {
        return $this->value;
    }

    public static function fromString(string $codePostal, CodePays $pays): self
    {
        return new self(trim($codePostal), $pays);
    }

    public static function tryFromString(?string $codePostal, ?CodePays $pays): ?self
    {
        if ($codePostal === null || $pays === null) {
            return null;
        }

        try {
            return self::fromString($codePostal, $pays);
        } catch (InvalidArgumentException) {
            return null;
        }
    }

    public function toString(): string
    {
        return $this->value;
    }

    public function getPays(): CodePays
    {
        return $this->pays;
    }

    public function formatForDisplay(): string
    {
        return $this->value; // Canada case removed as not defined in enum
    }

    public function formatWithSpaces(): string
    {
        return $this->value; // Canada case removed as not defined in enum
    }

    public function getDepartement(): ?string
    {
        if ($this->pays !== CodePays::France) {
            return null;
        }

        return substr($this->value, 0, 2);
    }

    public function getRegion(): ?string
    {
        if ($this->pays !== CodePays::France) {
            return null;
        }

        $dept = $this->getDepartement();

        // Mapping simplifié des départements vers régions
        return match (true) {
            in_array($dept, ['75', '77', '78', '91', '92', '93', '94', '95'])                               => 'Île-de-France',
            in_array($dept, ['13', '83', '84', '04', '05', '06'])                                           => 'Provence-Alpes-Côte d\'Azur',
            in_array($dept, ['69', '01', '07', '26', '38', '42', '73', '74'])                               => 'Auvergne-Rhône-Alpes',
            in_array($dept, ['31', '09', '11', '12', '30', '32', '34', '46', '48', '65', '66', '81', '82']) => 'Occitanie',
            default                                                                                         => 'Autre région',
        };
    }

    public function isParisRegion(): bool
    {
        if ($this->pays !== CodePays::France) {
            return false;
        }

        $dept = $this->getDepartement();

        return in_array($dept, ['75', '77', '78', '91', '92', '93', '94', '95']);
    }

    public function equals(CodePostal $other): bool
    {
        return $this->value === $other->value && $this->pays === $other->pays;
    }

    private function enforceInvariant(): void
    {
        $this->validateNotEmpty();
        $this->validateFormatForCountry();
    }

    private function validateNotEmpty(): void
    {
        if (!v::notEmpty()->validate(trim($this->value))) {
            throw new InvalidArgumentException('Le code postal ne peut pas être vide');
        }
    }

    private function validateFormatForCountry(): void
    {
        $isValid = match ($this->pays) {
            CodePays::France     => v::regex('/^\d{5}$/')->validate($this->value),
            CodePays::Belgique   => v::regex('/^\d{4}$/')->validate($this->value),
            CodePays::Luxembourg => v::regex('/^\d{4}$/')->validate($this->value),
            CodePays::Suisse     => v::regex('/^\d{4}$/')->validate($this->value),
            default              => v::regex('/^[A-Z0-9\s\-]{3,10}$/i')->validate($this->value),
        };

        if (!$isValid) {
            $expectedFormat = match ($this->pays) {
                CodePays::France     => '5 chiffres (ex: 75001)',
                CodePays::Belgique   => '4 chiffres (ex: 1000)',
                CodePays::Luxembourg => '4 chiffres (ex: 1234)',
                CodePays::Suisse     => '4 chiffres (ex: 1000)',
                default              => 'Format international',
            };

            throw new InvalidArgumentException(
                "Code postal invalid pour {$this->pays->value}: '{$this->value}'. " .
                "Format attendu: {$expectedFormat}"
            );
        }
    }
}
