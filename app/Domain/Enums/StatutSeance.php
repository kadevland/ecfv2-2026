<?php

declare(strict_types=1);

namespace App\Domain\Enums;

enum StatutSeance: string
{
    case PROGRAMMEE = 'PROGRAMMEE';
    case EN_COURS   = 'EN_COURS';
    case TERMINEE   = 'TERMINEE';
    case ANNULEE    = 'ANNULEE';

    public function label(): string
    {
        return match ($this) {
            self::PROGRAMMEE => 'Programmée',
            self::EN_COURS   => 'En cours',
            self::TERMINEE   => 'Terminée',
            self::ANNULEE    => 'Annulée',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::PROGRAMMEE => 'Séance programmée et disponible à la réservation',
            self::EN_COURS   => 'Séance en cours de projection',
            self::TERMINEE   => 'Séance terminée',
            self::ANNULEE    => 'Séance annulée par le cinéma',
        };
    }

    public function isBookable(): bool
    {
        return $this === self::PROGRAMMEE;
    }

    public function isActive(): bool
    {
        return in_array($this, [self::PROGRAMMEE, self::EN_COURS]);
    }

    public function isClosed(): bool
    {
        return in_array($this, [self::TERMINEE, self::ANNULEE]);
    }

    public function getColorClass(): string
    {
        return match ($this) {
            self::PROGRAMMEE => 'text-blue-600',
            self::EN_COURS   => 'text-green-600',
            self::TERMINEE   => 'text-gray-600',
            self::ANNULEE    => 'text-red-600',
        };
    }

    public function getBadgeClass(): string
    {
        return match ($this) {
            self::PROGRAMMEE => 'bg-blue-100 text-blue-800',
            self::EN_COURS   => 'bg-green-100 text-green-800',
            self::TERMINEE   => 'bg-gray-100 text-gray-800',
            self::ANNULEE    => 'bg-red-100 text-red-800',
        };
    }
}
