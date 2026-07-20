<?php

declare(strict_types=1);

namespace App\Infrastructure\Database\Schemas\Auth;

use App\Infrastructure\Database\Schemas\DatabaseSchemas;

final class UserSchema
{
    // Table configuration
    public const SCHEMA = DatabaseSchemas::AUTH;

    public const TABLE = 'users';

    public const FULL_TABLE = self::SCHEMA . '.' . self::TABLE;

    public const PRIMARY_KEY = 'db_id';

    // Auto-increment PK for performance (technical)
    public const DB_ID = 'db_id';

    // Domain identifier (business) - pattern DDD correct
    public const ID = 'id';

    public const CONNECTION = 'pgsql';

    // Columns
    public const TYPE = 'type';

    public const IS_ACTIVE = 'is_active';

    public const CREATED_AT = 'created_at';

    public const UPDATED_AT = 'updated_at';

    // Indexes
    public const INDEX_TYPE = 'idx_auth_users_type';

    public const INDEX_ACTIVE = 'idx_auth_users_active';

    public const INDEX_CREATED = 'idx_auth_users_created';

    // Constraints
    public const CONSTRAINT_TYPE_CHECK = 'users_type_check';
}
