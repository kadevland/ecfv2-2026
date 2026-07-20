<?php

declare(strict_types=1);

namespace App\Domain\Enums;

enum VersionFilm: string
{
    case VF   = 'VF';
    case VO   = 'VO';
    case VOST = 'VOST';

    public function label(): string
    {
        return match ($this) {
            self::VF   => 'Version Française',
            self::VO   => 'Version Originale',
            self::VOST => 'VO Sous-titrée Français',
        };
    }

    public function shortLabel(): string
    {
        return $this->value;
    }

    public function description(): string
    {
        return match ($this) {
            self::VF   => 'Film doublé en français',
            self::VO   => 'Film en langue originale sans sous-titres',
            self::VOST => 'Film en langue originale avec sous-titres français',
        };
    }

    public function hasSubtitles(): bool
    {
        return $this === self::VOST;
    }

    public function isDubbed(): bool
    {
        return $this === self::VF;
    }

    public function isOriginal(): bool
    {
        return in_array($this, [self::VO, self::VOST]);
    }
}
