<?php

declare(strict_types=1);

namespace App\Domain\Enums;

enum ClassificationFilm: string
{
    case TOUS_PUBLICS  = 'TOUS_PUBLICS';
    case AVERTISSEMENT = 'AVERTISSEMENT';
    case MOINS_12      = 'MOINS_12';
    case MOINS_16      = 'MOINS_16';
    case MOINS_18      = 'MOINS_18';

    public function label(): string
    {
        return match ($this) {
            self::TOUS_PUBLICS  => 'Tous publics',
            self::AVERTISSEMENT => 'Avertissement',
            self::MOINS_12      => '-12 ans',
            self::MOINS_16      => '-16 ans',
            self::MOINS_18      => '-18 ans',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::TOUS_PUBLICS  => 'Tous publics sans restriction',
            self::AVERTISSEMENT => 'Déconseillé aux jeunes enfants',
            self::MOINS_12      => 'Interdit aux moins de 12 ans',
            self::MOINS_16      => 'Interdit aux moins de 16 ans',
            self::MOINS_18      => 'Interdit aux moins de 18 ans',
        };
    }

    public function getMinAge(): int
    {
        return match ($this) {
            self::TOUS_PUBLICS  => 0,
            self::AVERTISSEMENT => 0,
            self::MOINS_12      => 12,
            self::MOINS_16      => 16,
            self::MOINS_18      => 18,
        };
    }

    public function canWatch(int $age): bool
    {
        return $age >= $this->getMinAge();
    }

    public function getColorClass(): string
    {
        return match ($this) {
            self::TOUS_PUBLICS  => 'text-green-600',
            self::AVERTISSEMENT => 'text-blue-600',
            self::MOINS_12      => 'text-yellow-600',
            self::MOINS_16      => 'text-orange-600',
            self::MOINS_18      => 'text-red-600',
        };
    }
}
