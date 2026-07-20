<?php

declare(strict_types=1);

namespace App\Domain\Enums;

enum Poste: string
{
    case MANAGER        = 'Manager';
    case EMPLOYE        = 'Employé';
    case TECHNICIEN     = 'Technicien';
    case ADMINISTRATEUR = 'Administrateur';

    public function label(): string
    {
        return $this->value;
    }

    public function description(): string
    {
        return match ($this) {
            self::MANAGER        => 'Responsable d\'équipe et de gestion',
            self::EMPLOYE        => 'Employé standard du cinéma',
            self::TECHNICIEN     => 'Technicien spécialisé équipements',
            self::ADMINISTRATEUR => 'Administrateur système et données',
        };
    }

    public function isManagement(): bool
    {
        return in_array($this, [self::MANAGER, self::ADMINISTRATEUR]);
    }

    public function requiresTechnicalSkills(): bool
    {
        return in_array($this, [self::TECHNICIEN, self::ADMINISTRATEUR]);
    }
}
