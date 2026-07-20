<?php

declare(strict_types=1);

namespace App\Domain\Enums;

enum StatutIncident: string
{
    case NOUVEAU  = 'nouveau';
    case EN_COURS = 'en_cours';
    case RESOLU   = 'resolu';
    case FERME    = 'ferme';

    public function label(): string
    {
        return match ($this) {
            self::NOUVEAU  => 'Nouveau',
            self::EN_COURS => 'En cours',
            self::RESOLU   => 'Résolu',
            self::FERME    => 'Fermé',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::NOUVEAU  => 'Incident nouvellement créé',
            self::EN_COURS => 'Incident en cours de traitement',
            self::RESOLU   => 'Incident résolu, en attente de validation',
            self::FERME    => 'Incident fermé définitivement',
        };
    }

    public function isOpen(): bool
    {
        return in_array($this, [self::NOUVEAU, self::EN_COURS]);
    }

    public function isClosed(): bool
    {
        return in_array($this, [self::RESOLU, self::FERME]);
    }

    public function canBeReopened(): bool
    {
        return $this === self::RESOLU;
    }

    /**
     * @return array<StatutIncident>
     */
    public function getNextPossibleStates(): array
    {
        return match ($this) {
            self::NOUVEAU  => [self::EN_COURS],
            self::EN_COURS => [self::RESOLU],
            self::RESOLU   => [self::FERME, self::EN_COURS], // Peut être rouvert
            self::FERME    => [], // Terminal
        };
    }
}
