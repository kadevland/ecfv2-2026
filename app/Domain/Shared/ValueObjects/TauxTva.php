<?php

declare(strict_types=1);

namespace App\Domain\Shared\ValueObjects;

use InvalidArgumentException;
use App\Domain\Shared\Enums\CodePays;
use Respect\Validation\Validator as v;

final readonly class TauxTva
{
    private const MAX_BASIS_POINTS = 10000; // 100%

    private const MIN_BASIS_POINTS = 0;

    public function __construct(
        public readonly int $basisPoints
    ) {
        $this->enforceInvariant();
    }

    public function __toString(): string
    {
        return $this->formatForDisplay();
    }

    public static function fromPercentage(float $percentage): self
    {
        if ($percentage < 0 || $percentage > 100) {
            throw new InvalidArgumentException('Le pourcentage doit être entre 0 et 100');
        }

        return new self((int) round($percentage * 100));
    }

    public static function fromBasisPoints(int $basisPoints): self
    {
        return new self($basisPoints);
    }

    public static function tryFromBasisPoints(?int $basisPoints): ?self
    {
        if ($basisPoints === null) {
            return null;
        }

        try {
            return new self($basisPoints);
        } catch (InvalidArgumentException) {
            return null;
        }
    }

    public static function fromCountryStandard(CodePays $pays): self
    {
        // Fixed rate based on country (standard VAT rates)
        $basisPoints = match ($pays) {
            CodePays::France     => 2000, // 20%
            CodePays::Belgique   => 2100, // 21%
            CodePays::Luxembourg => 1700, // 17%
            CodePays::Suisse     => 770, // 7.7%
            default              => 2000 // Default to French rate
        };

        return new self($basisPoints);
    }

    public static function fromCountryCulture(CodePays $pays): self
    {
        // Reduced rate for cultural goods
        $basisPoints = match ($pays) {
            CodePays::France     => 550, // 5.5%
            CodePays::Belgique   => 600, // 6%
            CodePays::Luxembourg => 300, // 3%
            CodePays::Suisse     => 250, // 2.5%
            default              => 550 // Default to French rate
        };

        return new self($basisPoints);
    }

    public static function zero(): self
    {
        return new self(0);
    }

    public static function tryFromPercentage(?float $percentage): ?self
    {
        if ($percentage === null) {
            return null;
        }

        try {
            return self::fromPercentage($percentage);
        } catch (InvalidArgumentException) {
            return null;
        }
    }

    /**
     * @param array{basis_points: int} $data
     */
    public static function fromArray(array $data): self
    {
        return new self($data['basis_points']);
    }

    public function getBasisPoints(): int
    {
        return $this->basisPoints;
    }

    public function getPercentage(): float
    {
        return $this->basisPoints / 100;
    }

    public function getDecimal(): float
    {
        return $this->basisPoints / 10000;
    }

    public function isZero(): bool
    {
        return $this->basisPoints === 0;
    }

    public function isStandardRate(CodePays $pays): bool
    {
        $standardRate = self::fromCountryStandard($pays);

        return $this->basisPoints === $standardRate->basisPoints;
    }

    public function isCultureRate(CodePays $pays): bool
    {
        $cultureRate = self::fromCountryCulture($pays);

        return $this->basisPoints === $cultureRate->basisPoints;
    }

    public function isReducedRate(): bool
    {
        return $this->basisPoints < 1500; // Moins de 15%
    }

    public function getLabel(): string
    {
        if ($this->isZero()) {
            return 'Exonéré de TVA';
        }

        return $this->getPercentage() . '% TVA';
    }

    public function getDescription(CodePays $pays): string
    {
        return match (true) {
            $this->isZero()              => 'Exonéré de TVA',
            $this->isStandardRate($pays) => "Taux standard {$pays->value}",
            $this->isCultureRate($pays)  => "Taux culture {$pays->value}",
            $this->isReducedRate()       => 'Taux réduit',
            default                      => 'Taux particulier',
        };
    }

    public function formatForDisplay(): string
    {
        if ($this->isZero()) {
            return '0%';
        }

        $percentage = $this->getPercentage();

        // Affichage avec décimales si nécessaire
        if ($percentage === floor($percentage)) {
            return (int) $percentage . '%';
        }

        return number_format($percentage, 1, ',', '') . '%';
    }

    /**
     * @return array{basis_points: int, percentage: float, decimal: float, label: string, formatted: string}
     */
    public function toArray(): array
    {
        return [
            'basis_points' => $this->basisPoints,
            'percentage'   => $this->getPercentage(),
            'decimal'      => $this->getDecimal(),
            'label'        => $this->getLabel(),
            'formatted'    => $this->formatForDisplay(),
        ];
    }

    public function equals(TauxTva $other): bool
    {
        return $this->basisPoints === $other->basisPoints;
    }

    public function isGreaterThan(TauxTva $other): bool
    {
        return $this->basisPoints > $other->basisPoints;
    }

    public function isLessThan(TauxTva $other): bool
    {
        return $this->basisPoints < $other->basisPoints;
    }

    public function compareTo(TauxTva $other): int
    {
        return match (true) {
            $this->basisPoints > $other->basisPoints => 1,
            $this->basisPoints < $other->basisPoints => -1,
            default                                  => 0,
        };
    }

    private function enforceInvariant(): void
    {
        $this->validateRange();
    }

    private function validateRange(): void
    {
        if (!v::intVal()->between(self::MIN_BASIS_POINTS, self::MAX_BASIS_POINTS)->validate($this->basisPoints)) {
            throw new InvalidArgumentException(
                'Le taux TVA doit être entre ' . self::MIN_BASIS_POINTS . ' et ' . self::MAX_BASIS_POINTS .
                " basis points (0-100%). Valeur fournie: {$this->basisPoints}"
            );
        }
    }
}
