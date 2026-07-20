<?php

declare(strict_types=1);

namespace App\Domain\Enums;

enum NiveauAcces: string
{
    case EMPLOYE        = 'employe';
    case SUPERVISEUR    = 'superviseur';
    case MANAGER        = 'manager';
    case ADMINISTRATEUR = 'administrateur';

    public function label(): string
    {
        return match ($this) {
            self::EMPLOYE        => 'Employé',
            self::SUPERVISEUR    => 'Superviseur',
            self::MANAGER        => 'Manager',
            self::ADMINISTRATEUR => 'Administrateur',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::EMPLOYE        => 'Accès de base aux fonctionnalités employé',
            self::SUPERVISEUR    => 'Supervision d\'équipe et reporting',
            self::MANAGER        => 'Gestion complète du cinéma',
            self::ADMINISTRATEUR => 'Accès administrateur système',
        };
    }

    public function getPermissionLevel(): int
    {
        return match ($this) {
            self::EMPLOYE        => 1,
            self::SUPERVISEUR    => 2,
            self::MANAGER        => 3,
            self::ADMINISTRATEUR => 4,
        };
    }

    public function canManage(self $targetLevel): bool
    {
        return $this->getPermissionLevel() > $targetLevel->getPermissionLevel();
    }
}
