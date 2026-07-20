<?php

declare(strict_types=1);

namespace App\Enums;

enum TokenType: string
{
    case MOBILE  = 'mobile';
    case DESKTOP = 'desktop';
    case API     = 'api';
    case TEST    = 'test';

    public function label(): string
    {
        return match ($this) {
            self::MOBILE  => 'Application Mobile',
            self::DESKTOP => 'Application Desktop',
            self::API     => 'API Externe',
            self::TEST    => 'Tests Automatisés',
        };
    }

    public function defaultExpiration(): int
    {
        return match ($this) {
            self::MOBILE  => 30, // 30 jours
            self::DESKTOP => 7,  // 7 jours
            self::API     => 365,    // 1 an
            self::TEST    => 1,     // 1 jour
        };
    }
}
