<?php

declare(strict_types=1);

namespace App\Domain\Enums;

enum StatutPlanning: string
{
    case PLANIFIE = 'planifie';
    case CONFIRME = 'confirme';
    case ABSENT   = 'absent';
    case REMPLACE = 'remplace';

    public function label(): string
    {
        return match ($this) {
            self::PLANIFIE => 'Planifié',
            self::CONFIRME => 'Confirmé',
            self::ABSENT   => 'Absent',
            self::REMPLACE => 'Remplacé',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::PLANIFIE => 'Service planifié, en attente de confirmation',
            self::CONFIRME => 'Service confirmé par l\'employé',
            self::ABSENT   => 'Employé absent sans remplacement',
            self::REMPLACE => 'Employé remplacé par un autre',
        };
    }

    public function isActive(): bool
    {
        return in_array($this, [self::PLANIFIE, self::CONFIRME, self::REMPLACE]);
    }

    public function requiresReplacement(): bool
    {
        return in_array($this, [self::ABSENT, self::REMPLACE]);
    }
}
