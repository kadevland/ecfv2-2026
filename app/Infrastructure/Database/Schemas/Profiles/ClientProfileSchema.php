<?php

declare(strict_types=1);

namespace App\Infrastructure\Database\Schemas\Profiles;

use App\Infrastructure\Database\Schemas\DatabaseSchemas;

/**
 * Schema constants for client_profiles table
 */
final class ClientProfileSchema
{
    // Table configuration
    public const SCHEMA = DatabaseSchemas::PROFILES;

    public const TABLE = 'client_profiles';

    public const FULL_TABLE = self::SCHEMA . '.' . self::TABLE;

    public const PRIMARY_KEY = 'id';

    public const CONNECTION = 'pgsql';

    // Columns
    public const ID = 'id';

    public const USER_DB_ID = 'user_db_id';

    public const USER_UUID = 'user_uuid';

    public const USER_KEY = self::USER_DB_ID;

    public const PRENOM = 'prenom';

    public const NOM = 'nom';

    public const TELEPHONE = 'telephone';

    public const DATE_NAISSANCE = 'date_naissance';

    public const ADRESSE_FACTURATION = 'adresse_facturation';

    public const ADRESSE_LIVRAISON = 'adresse_livraison';

    public const PREFERENCES_COMMUNICATION = 'preferences_communication';

    public const DONNEES_ANONYMISEES_LE = 'donnees_anonymisees_le';

    public const DERNIERE_ACTIVITE = 'derniere_activite';

    public const CREATED_AT = 'created_at';

    public const UPDATED_AT = 'updated_at';
}
