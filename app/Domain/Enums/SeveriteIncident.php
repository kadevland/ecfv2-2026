<?php

declare(strict_types=1);

namespace App\Domain\Enums;

enum SeveriteIncident: string
{
    case FAIBLE   = 'faible';
    case MOYENNE  = 'moyenne';
    case HAUTE    = 'haute';
    case CRITIQUE = 'critique';

    public function label(): string
    {
        return match ($this) {
            self::FAIBLE   => 'Faible',
            self::MOYENNE  => 'Moyenne',
            self::HAUTE    => 'Haute',
            self::CRITIQUE => 'Critique',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::FAIBLE   => 'Impact limité, peut attendre',
            self::MOYENNE  => 'Impact modéré, traitement sous 24h',
            self::HAUTE    => 'Impact important, traitement urgent',
            self::CRITIQUE => 'Impact majeur, traitement immédiat',
        };
    }

    public function getMaxResolutionTime(): int
    {
        return match ($this) {
            self::FAIBLE   => 7 * 24, // 7 jours en heures
            self::MOYENNE  => 24, // 1 jour
            self::HAUTE    => 4, // 4 heures
            self::CRITIQUE => 1, // 1 heure
        };
    }

    public function getColorClass(): string
    {
        return match ($this) {
            self::FAIBLE   => 'text-green-600',
            self::MOYENNE  => 'text-yellow-600',
            self::HAUTE    => 'text-orange-600',
            self::CRITIQUE => 'text-red-600',
        };
    }

    public function requiresManagerNotification(): bool
    {
        return in_array($this, [self::HAUTE, self::CRITIQUE]);
    }
}
