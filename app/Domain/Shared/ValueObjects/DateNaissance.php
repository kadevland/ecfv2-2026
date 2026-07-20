<?php

declare(strict_types=1);

namespace App\Domain\Shared\ValueObjects;

use DateTimeImmutable;
use InvalidArgumentException;

final readonly class DateNaissance
{
    private const MAX_AGE = 150;

    private const MIN_AGE = 0;

    public function __construct(
        public readonly DateTimeImmutable $value
    ) {
        $this->enforceInvariant();
    }

    public function __toString(): string
    {
        return $this->value->format('Y-m-d');
    }

    public static function fromString(string $dateString): self
    {
        $date = DateTimeImmutable::createFromFormat('Y-m-d', $dateString);

        if ($date === false) {
            throw new InvalidArgumentException('Format de date invalide. Attendu: Y-m-d');
        }

        return new self($date);
    }

    public static function fromDate(DateTimeImmutable $date): self
    {
        return new self($date);
    }

    public static function tryFromString(?string $dateString): ?self
    {
        if ($dateString === null) {
            return null;
        }

        try {
            return self::fromString($dateString);
        } catch (InvalidArgumentException) {
            return null;
        }
    }

    public function toDate(): DateTimeImmutable
    {
        return $this->value;
    }

    public function toString(): string
    {
        return $this->value->format('Y-m-d');
    }

    public function toDisplayFormat(): string
    {
        return $this->value->format('d/m/Y');
    }

    public function getAge(): int
    {
        $today = new DateTimeImmutable;

        return (int) $this->value->diff($today)->y;
    }

    public function getAgeAt(DateTimeImmutable $referenceDate): int
    {
        if ($referenceDate < $this->value) {
            throw new InvalidArgumentException('Date de référence antérieure à la naissance');
        }

        return (int) $this->value->diff($referenceDate)->y;
    }

    public function isMinor(): bool
    {
        return $this->getAge() < 18;
    }

    public function isSenior(): bool
    {
        return $this->getAge() >= 65;
    }

    public function isChild(): bool
    {
        return $this->getAge() < 12;
    }

    public function isAdult(): bool
    {
        return $this->getAge() >= 18;
    }

    public function canWatchClassification(int $minAge): bool
    {
        return $this->getAge() >= $minAge;
    }

    public function getAgeGroup(): string
    {
        $age = $this->getAge();

        return match (true) {
            $age < 12 => 'enfant',
            $age < 18 => 'adolescent',
            $age < 65 => 'adulte',
            default   => 'senior',
        };
    }

    public function getBirthYear(): int
    {
        return (int) $this->value->format('Y');
    }

    public function getBirthMonth(): int
    {
        return (int) $this->value->format('n');
    }

    public function getBirthDay(): int
    {
        return (int) $this->value->format('j');
    }

    public function isBirthdayToday(): bool
    {
        $today = new DateTimeImmutable;

        return $this->value->format('m-d') === $today->format('m-d');
    }

    public function isBirthdayThisMonth(): bool
    {
        $today = new DateTimeImmutable;

        return $this->value->format('m') === $today->format('m');
    }

    public function getNextBirthday(): DateTimeImmutable
    {
        $today    = new DateTimeImmutable;
        $thisYear = (int) $today->format('Y');

        $nextBirthday = DateTimeImmutable::createFromFormat(
            'Y-m-d',
            $thisYear . '-' . $this->value->format('m-d')
        );

        if ($nextBirthday < $today) {
            $nextBirthday = $nextBirthday->modify('+1 year');
        }

        return $nextBirthday;
    }

    public function equals(DateNaissance $other): bool
    {
        return $this->value->format('Y-m-d') === $other->value->format('Y-m-d');
    }

    private function enforceInvariant(): void
    {
        $this->validateNotFuture();
        $this->validateMaxAge();
        $this->validateMinAge();
    }

    private function validateNotFuture(): void
    {
        $today = new DateTimeImmutable;
        if ($this->value > $today) {
            throw new InvalidArgumentException('La date de naissance ne peut pas être dans le futur');
        }
    }

    private function validateMaxAge(): void
    {
        $today         = new DateTimeImmutable;
        $oldestAllowed = $today->modify('-' . self::MAX_AGE . ' years');

        if ($this->value < $oldestAllowed) {
            throw new InvalidArgumentException("L'âge maximum autorisé est de " . self::MAX_AGE . ' ans');
        }
    }

    private function validateMinAge(): void
    {
        $today           = new DateTimeImmutable;
        $youngestAllowed = $today->modify('-' . self::MIN_AGE . ' years');

        if ($this->value > $youngestAllowed) {
            throw new InvalidArgumentException('La date de naissance est trop récente');
        }
    }
}
