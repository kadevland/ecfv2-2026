<?php

declare(strict_types=1);

namespace App\Domain\Enums;

enum TempsTravail: string
{
    case TEMPS_PLEIN   = 'Temps plein';
    case TEMPS_PARTIEL = 'Temps partiel';
    case VARIABLE      = 'Variable';

    public function label(): string
    {
        return $this->value;
    }

    public function description(): string
    {
        return match ($this) {
            self::TEMPS_PLEIN   => '35h ou plus par semaine',
            self::TEMPS_PARTIEL => 'Moins de 35h par semaine',
            self::VARIABLE      => 'Horaires variables selon planning',
        };
    }

    public function getMinHoursPerWeek(): ?int
    {
        return match ($this) {
            self::TEMPS_PLEIN   => 35,
            self::TEMPS_PARTIEL => 1,
            self::VARIABLE      => null,
        };
    }

    public function getMaxHoursPerWeek(): int
    {
        return match ($this) {
            self::TEMPS_PLEIN   => 48, // Limite légale
            self::TEMPS_PARTIEL => 34,
            self::VARIABLE      => 48,
        };
    }
}
