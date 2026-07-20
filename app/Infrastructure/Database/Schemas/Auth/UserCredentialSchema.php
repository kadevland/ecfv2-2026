<?php

declare(strict_types=1);

namespace App\Infrastructure\Database\Schemas\Auth;

final class UserCredentialSchema
{
    // Table configuration
    public const SCHEMA = 'auth';

    public const TABLE = 'user_credentials';

    public const FULL_TABLE = self::SCHEMA . '.' . self::TABLE;

    public const PRIMARY_KEY = 'id';

    public const CONNECTION = 'pgsql';

    // Columns
    public const ID = 'id';

    public const USER_ID = 'user_uuid'; // FK business vers users.uuid

    public const USER_KEY = 'user_db_id'; // FK performance vers users.db_id

    public const EMAIL = 'email';

    public const PASSWORD_HASH = 'password_hash';

    public const EMAIL_VERIFIED_AT = 'email_verified_at';

    public const REMEMBER_TOKEN = 'remember_token';

    public const CREATED_AT = 'created_at';

    public const UPDATED_AT = 'updated_at';

    // Indexes
    public const INDEX_USER_ID = 'idx_auth_credentials_user';

    public const INDEX_EMAIL = 'idx_auth_credentials_email';

    // Foreign Keys
    public const FK_USER_ID = 'fk_credentials_user_uuid';
}
