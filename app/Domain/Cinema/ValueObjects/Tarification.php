<?php

declare(strict_types=1);

namespace App\Domain\Cinema\ValueObjects;

use ValueError;
use Money\Money;
use InvalidArgumentException;
use App\Domain\Cinema\Enums\TypeTarifEnum;
use App\Domain\Shared\ValueObjects\MoneyHelper;

final readonly class Tarification
{
    public const TARIFS_BASE = 'tarifs_base';

    public const SUPPLEMENTS_SPECIAUX = 'supplements_speciaux';

    public const REDUCTIONS_SPECIALES = 'reductions_speciales';

    /**
     * @param array<string, mixed> $tarifsBase
     * @param array<string, mixed>|null $supplementsSpeciaux
     * @param array<string, mixed>|null $reductionsSpeciales
     */
    public function __construct(
        public readonly array $tarifsBase,
        public readonly ?array $supplementsSpeciaux = null,
        public readonly ?array $reductionsSpeciales = null,
    ) {
        $this->enforceInvariant();
    }

    /**
     * @param array<string, mixed> $tarifsBase
     * @param array<string, mixed>|null $supplementsSpeciaux
     * @param array<string, mixed>|null $reductionsSpeciales
     */
    public static function create(array $tarifsBase, ?array $supplementsSpeciaux = null, ?array $reductionsSpeciales = null): self
    {
        return new self($tarifsBase, $supplementsSpeciaux, $reductionsSpeciales);
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            tarifsBase: $data[self::TARIFS_BASE] ?? [],
            supplementsSpeciaux: $data[self::SUPPLEMENTS_SPECIAUX] ?? null,
            reductionsSpeciales: $data[self::REDUCTIONS_SPECIALES] ?? null,
        );
    }

    /**
     * @param array<string, mixed>|null $data
     */
    public static function tryFromArray(?array $data): ?self
    {
        if ($data === null) {
            return null;
        }

        try {
            return self::fromArray($data);
        } catch (InvalidArgumentException) {
            return null;
        }
    }

    public function getPrixForType(TypeTarifEnum $type): ?Money
    {
        $prix = $this->tarifsBase[$type->value] ?? null;

        if ($prix === null) {
            return null;
        }

        return is_array($prix) ? MoneyHelper::fromArray($prix) : Money::EUR($prix);
    }

    public function getPrixNormal(): ?Money
    {
        return $this->getPrixForType(TypeTarifEnum::NORMAL);
    }

    public function getPrixReduit(): ?Money
    {
        return $this->getPrixForType(TypeTarifEnum::REDUIT);
    }

    public function getPrixSenior(): ?Money
    {
        return $this->getPrixForType(TypeTarifEnum::SENIOR);
    }

    public function getPrixEnfant(): ?Money
    {
        return $this->getPrixForType(TypeTarifEnum::ENFANT);
    }

    public function getPrixPMR(): ?Money
    {
        return $this->getPrixForType(TypeTarifEnum::GROUPE); // Using GROUPE as PMR equivalent
    }

    public function hasTarif(TypeTarifEnum $type): bool
    {
        return isset($this->tarifsBase[$type->value]);
    }

    /**
     * @return array<TypeTarifEnum>
     */
    public function getTypesDisponibles(): array
    {
        return array_map(
            fn ($value) => TypeTarifEnum::from($value),
            array_keys($this->tarifsBase)
        );
    }

    public function getPrixMinimum(): ?Money
    {
        $prix = array_filter(array_map(
            fn ($type) => $this->getPrixForType($type),
            $this->getTypesDisponibles()
        ));

        if (empty($prix)) {
            return null;
        }

        return array_reduce($prix, function (?Money $min, Money $current) {
            return $min === null || MoneyHelper::compare($current, $min) < 0 ? $current : $min;
        });
    }

    public function getPrixMaximum(): ?Money
    {
        $prix = array_filter(array_map(
            fn ($type) => $this->getPrixForType($type),
            $this->getTypesDisponibles()
        ));

        if (empty($prix)) {
            return null;
        }

        return array_reduce($prix, function (?Money $max, Money $current) {
            return $max === null || MoneyHelper::compare($current, $max) > 0 ? $current : $max;
        });
    }

    public function appliquerSupplement(string $typeSupplement): ?Money
    {
        if ($this->supplementsSpeciaux === null || !isset($this->supplementsSpeciaux[$typeSupplement])) {
            return null;
        }

        $supplement = $this->supplementsSpeciaux[$typeSupplement];

        return is_array($supplement) ? MoneyHelper::fromArray($supplement) : Money::EUR($supplement);
    }

    public function appliquerReduction(string $typeReduction): ?Money
    {
        if ($this->reductionsSpeciales === null || !isset($this->reductionsSpeciales[$typeReduction])) {
            return null;
        }

        $reduction = $this->reductionsSpeciales[$typeReduction];

        return is_array($reduction) ? MoneyHelper::fromArray($reduction) : Money::EUR($reduction);
    }

    public function calculerPrixFinal(TypeTarifEnum $typeTarif, ?string $supplement = null, ?string $reduction = null): ?Money
    {
        $prixBase = $this->getPrixForType($typeTarif);

        if ($prixBase === null) {
            return null;
        }

        $prixFinal = $prixBase;

        if ($supplement !== null) {
            $montantSupplement = $this->appliquerSupplement($supplement);
            if ($montantSupplement !== null) {
                $prixFinal = $prixFinal->add($montantSupplement);
            }
        }

        if ($reduction !== null) {
            $montantReduction = $this->appliquerReduction($reduction);
            if ($montantReduction !== null) {
                $prixFinal = $prixFinal->subtract($montantReduction);
            }
        }

        return $prixFinal;
    }

    public function estGratuit(): bool
    {
        foreach ($this->getTypesDisponibles() as $type) {
            $prix = $this->getPrixForType($type);
            if ($prix !== null && !MoneyHelper::isZero($prix)) {
                return false;
            }
        }

        return true;
    }

    public function equals(Tarification $autre): bool
    {
        return $this->toArray() === $autre->toArray();
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $result = [
            self::TARIFS_BASE => [],
        ];

        // Convert Money objects back to array format for storage
        foreach ($this->tarifsBase as $type => $prix) {
            if ($prix instanceof Money) {
                $result[self::TARIFS_BASE][$type] = MoneyHelper::toArray($prix);
            } else {
                $result[self::TARIFS_BASE][$type] = $prix;
            }
        }

        if ($this->supplementsSpeciaux !== null) {
            $result[self::SUPPLEMENTS_SPECIAUX] = $this->supplementsSpeciaux;
        }

        if ($this->reductionsSpeciales !== null) {
            $result[self::REDUCTIONS_SPECIALES] = $this->reductionsSpeciales;
        }

        return $result;
    }

    /**
     * @return array<string, float>
     */
    public function toSimpleArray(): array
    {
        $result = [];

        foreach ($this->getTypesDisponibles() as $type) {
            $prix = $this->getPrixForType($type);
            if ($prix !== null) {
                $result[$type->value] = (float) $prix->getAmount() / 100; // Convert cents to euros
            }
        }

        return $result;
    }

    private function enforceInvariant(): void
    {
        $this->validateTarifsBase();
        $this->validateTarifTypes();
        $this->validatePositivePrices();
    }

    private function validateTarifsBase(): void
    {
        if (empty($this->tarifsBase)) {
            throw new InvalidArgumentException('Au moins un tarif de base doit être défini');
        }
    }

    private function validateTarifTypes(): void
    {
        foreach ($this->tarifsBase as $type => $prix) {
            try {
                TypeTarifEnum::from($type);
            } catch (ValueError) {
                throw new InvalidArgumentException("Type de tarif invalide: {$type}");
            }
        }
    }

    private function validatePositivePrices(): void
    {
        foreach ($this->tarifsBase as $type => $prix) {
            if (is_array($prix)) {
                $money = MoneyHelper::fromArray($prix);
                if (!MoneyHelper::isPositive($money) && !MoneyHelper::isZero($money)) {
                    throw new InvalidArgumentException("Le prix pour le type {$type} doit être positif ou zéro");
                }
            } elseif (is_numeric($prix) && $prix < 0) {
                throw new InvalidArgumentException("Le prix pour le type {$type} doit être positif ou zéro");
            }
        }
    }
}
