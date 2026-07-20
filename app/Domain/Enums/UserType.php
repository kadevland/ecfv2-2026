<?php

declare(strict_types=1);

namespace App\Domain\Enums;

enum UserType: string
{
    case CLIENT       = 'client';
    case EMPLOYEE     = 'employee';
    case ADMIN        = 'admin';
    case USER_DELETED = 'user_deleted';

    /**
     * @return array<UserType>
     */
    public static function getActiveTypes(): array
    {
        return [
            self::CLIENT,
            self::EMPLOYEE,
            self::ADMIN,
        ];
    }

    public function label(): string
    {
        return match ($this) {
            self::CLIENT       => 'Client',
            self::EMPLOYEE     => 'Employé',
            self::ADMIN        => 'Administrateur',
            self::USER_DELETED => 'Utilisateur supprimé',
        };
    }

    public function isActive(): bool
    {
        return $this !== self::USER_DELETED;
    }
}
