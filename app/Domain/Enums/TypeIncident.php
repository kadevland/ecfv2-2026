<?php

declare(strict_types=1);

namespace App\Domain\Enums;

enum TypeIncident: string
{
    case TECHNIQUE = 'technique';
    case SECURITE  = 'securite';
    case CLIENT    = 'client';

    public function label(): string
    {
        return match ($this) {
            self::TECHNIQUE => 'Technique',
            self::SECURITE  => 'Sécurité',
            self::CLIENT    => 'Client',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::TECHNIQUE => 'Problème technique (équipement, son, image)',
            self::SECURITE  => 'Incident de sécurité (évacuation, alarme)',
            self::CLIENT    => 'Incident lié aux clients (comportement, plainte)',
        };
    }

    public function getDefaultSeverity(): SeveriteIncident
    {
        return match ($this) {
            self::TECHNIQUE => SeveriteIncident::MOYENNE,
            self::SECURITE  => SeveriteIncident::HAUTE,
            self::CLIENT    => SeveriteIncident::FAIBLE,
        };
    }

    public function requiresImmediateAction(): bool
    {
        return $this === self::SECURITE;
    }
}
