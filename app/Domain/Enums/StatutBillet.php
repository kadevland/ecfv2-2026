<?php

declare(strict_types=1);

namespace App\Domain\Enums;

enum StatutBillet: string
{
    case VALIDE  = 'valide';
    case UTILISE = 'utilise';
    case ANNULE  = 'annule';
    case EXPIRE  = 'expire';

    public function label(): string
    {
        return match ($this) {
            self::VALIDE  => 'Valide',
            self::UTILISE => 'Utilisé',
            self::ANNULE  => 'Annulé',
            self::EXPIRE  => 'Expiré',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::VALIDE  => 'Billet valide, prêt à être utilisé',
            self::UTILISE => 'Billet scanné et utilisé pour l\'entrée',
            self::ANNULE  => 'Billet annulé suite à annulation réservation',
            self::EXPIRE  => 'Billet expiré après la séance',
        };
    }

    public function isUsable(): bool
    {
        return $this === self::VALIDE;
    }

    public function isUsed(): bool
    {
        return $this === self::UTILISE;
    }

    public function isCancelled(): bool
    {
        return in_array($this, [self::ANNULE, self::EXPIRE]);
    }

    public function canBeScanned(): bool
    {
        return $this === self::VALIDE;
    }

    public function canBeRefunded(): bool
    {
        return in_array($this, [self::VALIDE, self::EXPIRE]);
    }

    public function getColorClass(): string
    {
        return match ($this) {
            self::VALIDE  => 'text-green-600',
            self::UTILISE => 'text-blue-600',
            self::ANNULE  => 'text-red-600',
            self::EXPIRE  => 'text-gray-600',
        };
    }

    public function getBadgeClass(): string
    {
        return match ($this) {
            self::VALIDE  => 'bg-green-100 text-green-800',
            self::UTILISE => 'bg-blue-100 text-blue-800',
            self::ANNULE  => 'bg-red-100 text-red-800',
            self::EXPIRE  => 'bg-gray-100 text-gray-800',
        };
    }

    public function getIconClass(): string
    {
        return match ($this) {
            self::VALIDE  => 'check-circle',
            self::UTILISE => 'check-circle-fill',
            self::ANNULE  => 'x-circle',
            self::EXPIRE  => 'clock',
        };
    }

    /**
     * @return array<StatutBillet>
     */
    public function getNextPossibleStates(): array
    {
        return match ($this) {
            self::VALIDE => [self::UTILISE, self::ANNULE, self::EXPIRE],
            self::UTILISE, self::ANNULE, self::EXPIRE => [], // États terminaux
        };
    }
}
