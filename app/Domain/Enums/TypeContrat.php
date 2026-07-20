<?php

declare(strict_types=1);

namespace App\Domain\Enums;

enum TypeContrat: string
{
    case CDI     = 'CDI';
    case CDD     = 'CDD';
    case STAGE   = 'Stage';
    case INTERIM = 'Interim';

    public function label(): string
    {
        return $this->value;
    }

    public function description(): string
    {
        return match ($this) {
            self::CDI     => 'Contrat à Durée Indéterminée',
            self::CDD     => 'Contrat à Durée Déterminée',
            self::STAGE   => 'Stage conventionné',
            self::INTERIM => 'Contrat d\'intérim',
        };
    }

    public function isPermanent(): bool
    {
        return $this === self::CDI;
    }

    public function requiresEndDate(): bool
    {
        return in_array($this, [self::CDD, self::STAGE, self::INTERIM]);
    }

    public function isEligibleForBenefits(): bool
    {
        return in_array($this, [self::CDI, self::CDD]);
    }
}
