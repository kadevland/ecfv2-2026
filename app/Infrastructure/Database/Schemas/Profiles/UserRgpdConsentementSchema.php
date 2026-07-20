<?php

declare(strict_types=1);

namespace App\Infrastructure\Database\Schemas\Profiles;

use App\Infrastructure\Database\Schemas\DatabaseSchemas;

/**
 * Schema constants for user_rgpd_consentement table
 * Gestion des consentements RGPD (newsletter, alertes, etc.)
 */
final class UserRgpdConsentementSchema
{
    // Table configuration
    public const SCHEMA = DatabaseSchemas::PROFILES;

    public const TABLE = 'user_rgpd_consentement';

    public const FULL_TABLE = self::SCHEMA . '.' . self::TABLE;

    public const PRIMARY_KEY = 'id';

    public const CONNECTION = 'pgsql';

    // Columns
    public const ID = 'id';

    public const USER_DB_ID = 'user_db_id'; // FK technique vers users.db_id

    public const USER_UUID = 'user_uuid'; // FK business vers users.id

    public const USER_PROFIL_ID = 'user_profil_id'; // FK vers profiles.user_profil.id

    public const TYPE_CONSENTEMENT = 'type_consentement';

    public const CONSENTEMENT_DONNE = 'consentement_donne';

    public const DATE_CONSENTEMENT = 'date_consentement';

    public const DATE_RETRAIT = 'date_retrait';

    public const CANAL_COLLECTE = 'canal_collecte';

    public const IP_COLLECTE = 'ip_collecte';

    public const CONTEXTE_COLLECTE = 'contexte_collecte';

    public const IP_CONSENTEMENT = 'ip_consentement';

    public const USER_AGENT = 'user_agent';

    public const CREATED_AT = 'created_at';

    public const UPDATED_AT = 'updated_at';

    // Indexes
    public const INDEX_USER_UUID = 'idx_rgpd_consentement_user_uuid';

    public const INDEX_USER_DB_ID = 'idx_rgpd_consentement_user_db_id';

    public const INDEX_TYPE_CONSENTEMENT = 'idx_rgpd_consentement_type';

    public const INDEX_CONSENTEMENT_DONNE = 'idx_rgpd_consentement_donne';

    public const INDEX_DATE_CONSENTEMENT = 'idx_rgpd_consentement_date';

    // Foreign Keys
    public const FK_USER_DB_ID = 'fk_rgpd_consentement_user_db_id';

    public const FK_USER_UUID = 'fk_rgpd_consentement_user_uuid';

    // Constraints
    public const CONSTRAINT_TYPE_CHECK = 'chk_rgpd_consentement_type';

    public const UNIQUE_USER_TYPE = 'uniq_rgpd_consentement_user_type';
}
