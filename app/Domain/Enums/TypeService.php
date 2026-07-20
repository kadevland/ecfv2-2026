<?php

declare(strict_types=1);

namespace App\Domain\Enums;

enum TypeService: string
{
    case MATIN      = 'matin';
    case APRES_MIDI = 'apres-midi';
    case SOIREE     = 'soiree';
    case NUIT       = 'nuit';

    public function label(): string
    {
        return match ($this) {
            self::MATIN      => 'Matin',
            self::APRES_MIDI => 'Après-midi',
            self::SOIREE     => 'Soirée',
            self::NUIT       => 'Nuit',
        };
    }

    public function getTypicalStartTime(): string
    {
        return match ($this) {
            self::MATIN      => '08:00',
            self::APRES_MIDI => '14:00',
            self::SOIREE     => '18:00',
            self::NUIT       => '22:00',
        };
    }

    public function getTypicalEndTime(): string
    {
        return match ($this) {
            self::MATIN      => '14:00',
            self::APRES_MIDI => '18:00',
            self::SOIREE     => '23:00',
            self::NUIT       => '02:00',
        };
    }

    public function isWeekendService(): bool
    {
        return in_array($this, [self::SOIREE, self::NUIT]);
    }
}
