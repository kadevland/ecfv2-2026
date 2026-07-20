<?php

declare(strict_types=1);

namespace App\Enums;

enum UserType: string
{
    case CLIENT         = 'client';
    case EMPLOYEE       = 'employee';
    case ADMIN          = 'admin';
    case CLIENT_DELETED = 'client_deleted';

    /**
     * Summary of activeTypes
     *
     * @return UserType[]
     */
    public static function activeTypes(): array
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
            self::CLIENT         => 'Client',
            self::EMPLOYEE       => 'Employé',
            self::ADMIN          => 'Administrateur',
            self::CLIENT_DELETED => 'Client supprimé',
        };
    }

    public function isActive(): bool
    {
        return $this !== self::CLIENT_DELETED;
    }
}
