<?php

declare(strict_types=1);

namespace App\Infrastructure\Database\Schemas\Profiles;

use App\Infrastructure\Database\Schemas\DatabaseSchemas;

/**
 * Schema constants for user_rgpd_profil table
 * Profil de substitution pour utilisateurs supprimés (RGPD droit à l'oubli)
 */
final class UserRgpdProfilSchema
{
    // Table configuration
    public const SCHEMA = DatabaseSchemas::PROFILES;

    public const TABLE = 'user_rgpd_profil';

    public const FULL_TABLE = self::SCHEMA . '.' . self::TABLE;

    public const PRIMARY_KEY = 'id';

    public const CONNECTION = 'pgsql';

    // Columns
    public const ID = 'id';

    public const USER_DB_ID = 'user_db_id'; // FK technique vers users.db_id

    public const USER_UUID = 'user_uuid'; // FK business vers users.id

    public const USER_UUID_ORIGINAL = 'user_uuid_original'; // UUID original avant suppression

    public const USER_ID_ORIGINAL = 'user_uuid_original'; // Alias pour compatibilité

    public const NOM_SUBSTITUTION = 'nom_substitution';

    public const PRENOM_SUBSTITUTION = 'prenom_substitution';

    public const EMAIL_SUBSTITUTION = 'email_substitution';

    public const DATE_SUPPRESSION_DEMANDEE = 'date_suppression_demandee';

    public const DATE_SUPPRESSION_EFFECTIVE = 'date_suppression_effective';

    public const ORIGIN_TYPE = 'origin_type'; // Type utilisateur avant suppression

    public const DATE_SUPPRESSION = 'date_suppression';

    public const RAISON_SUPPRESSION = 'raison_suppression';

    public const OPERATEUR_SUPPRESSION = 'operateur_suppression';

    public const COMMENTAIRE_INTERNE = 'commentaire_interne';

    public const AVAIT_RESERVATIONS = 'avait_reservations';

    public const NOMBRE_RESERVATIONS_HISTORIQUE = 'nombre_reservations_historique';

    public const DONNEES_CONSERVEES = 'donnees_conservees'; // JSONB données minimales légales

    public const CREATED_AT = 'created_at';

    public const UPDATED_AT = 'updated_at';

    // Indexes
    public const INDEX_USER_UUID = 'idx_rgpd_profil_user_uuid';

    public const INDEX_USER_DB_ID = 'idx_rgpd_profil_user_db_id';

    public const INDEX_DATE_SUPPRESSION = 'idx_rgpd_profil_date_suppression';

    public const INDEX_ORIGIN_TYPE = 'idx_rgpd_profil_origin_type';

    public const INDEX_DONNEES_CONSERVEES = 'idx_rgpd_profil_donnees_conservees'; // GIN index sur JSONB

    // Foreign Keys
    public const FK_USER_DB_ID = 'fk_rgpd_profil_user_db_id';

    public const FK_USER_UUID = 'fk_rgpd_profil_user_uuid';

    // Constraints
    public const CONSTRAINT_ORIGIN_TYPE_CHECK = 'chk_rgpd_profil_origin_type';
}
