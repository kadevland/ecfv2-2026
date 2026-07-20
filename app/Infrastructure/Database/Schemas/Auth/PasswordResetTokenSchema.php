<?php

declare(strict_types=1);

namespace App\Infrastructure\Database\Schemas\Auth;

use App\Infrastructure\Database\Schemas\DatabaseSchemas;

final class PasswordResetTokenSchema
{
    public const SCHEMA = DatabaseSchemas::AUTH;

    public const TABLE = 'password_reset_tokens';

    public const FULL_TABLE = self::SCHEMA . '.' . self::TABLE;

    public const CONNECTION = 'pgsql';

    // Column names
    public const EMAIL = 'email';

    public const TOKEN = 'token';

    public const CREATED_AT = 'created_at';
}
