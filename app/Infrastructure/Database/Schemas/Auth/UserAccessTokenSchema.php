<?php

declare(strict_types=1);

namespace App\Infrastructure\Database\Schemas\Auth;

final class UserAccessTokenSchema
{
    // Table configuration
    public const SCHEMA = 'auth';

    public const TABLE = 'user_access_tokens';

    public const FULL_TABLE = self::SCHEMA . '.' . self::TABLE;

    public const PRIMARY_KEY = 'id';

    public const CONNECTION = 'pgsql';

    // Columns
    public const ID = 'id';

    public const USER_ID = 'user_uuid'; // FK business vers users.uuid

    public const USER_KEY = 'user_db_id'; // FK performance vers users.db_id

    public const TOKEN_TYPE = 'token_type';

    public const TOKEN = 'token';

    public const NAME = 'name';

    public const LAST_USED_AT = 'last_used_at';

    public const EXPIRES_AT = 'expires_at';

    public const CREATED_AT = 'created_at';

    public const UPDATED_AT = 'updated_at';

    // Indexes
    public const INDEX_USER_ID = 'idx_auth_tokens_user';

    public const INDEX_TOKEN = 'idx_auth_tokens_token';

    public const INDEX_EXPIRES = 'idx_auth_tokens_expires';

    // Foreign Keys
    public const FK_USER_ID = 'fk_tokens_user_uuid';

    // Constraints
    public const CONSTRAINT_TOKEN_TYPE_CHECK = 'tokens_type_check';
}
