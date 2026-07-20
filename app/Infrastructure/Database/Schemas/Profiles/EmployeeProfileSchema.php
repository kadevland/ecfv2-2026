<?php

declare(strict_types=1);

namespace App\Infrastructure\Database\Schemas\Profiles;

use App\Infrastructure\Database\Schemas\DatabaseSchemas;

/**
 * Schema constants for employee_profiles table
 * Profils professionnels pour les employés de cinémas
 */
final class EmployeeProfileSchema
{
    // Table configuration
    public const SCHEMA = DatabaseSchemas::PROFILES;

    public const TABLE = 'employee_profiles';

    public const FULL_TABLE = self::SCHEMA . '.' . self::TABLE;

    public const PRIMARY_KEY = 'id';

    public const CONNECTION = 'pgsql';

    // Columns
    public const ID = 'id';

    public const USER_DB_ID = 'user_db_id'; // FK technique vers users.db_id

    public const USER_UUID = 'user_uuid'; // FK business vers users.id

    public const NOM = 'nom';

    public const PRENOM = 'prenom';

    public const EMAIL_PROFESSIONNEL = 'email_professionnel';

    public const TELEPHONE_PROFESSIONNEL = 'telephone_professionnel';

    public const NUMERO_EMPLOYE = 'numero_employe';

    public const DATE_EMBAUCHE = 'date_embauche';

    public const POSTE = 'poste';

    public const DEPARTEMENT = 'departement';

    public const CINEMA_ID = 'cinema_id';

    public const CINEMA_KEY = 'cinema_id';

    public const RESPONSABLE_UUID = 'responsable_uuid';

    public const CREATED_AT = 'created_at';

    public const UPDATED_AT = 'updated_at';

    // Indexes
    public const INDEX_USER_UUID = 'idx_employee_profile_user_uuid';

    public const INDEX_USER_DB_ID = 'idx_employee_profile_user_db_id';

    public const INDEX_EMAIL = 'idx_employee_profile_email';

    public const INDEX_NUMERO_EMPLOYE = 'idx_employee_profile_numero_employe';

    public const INDEX_NOM_PRENOM = 'idx_employee_profile_nom_prenom';

    public const INDEX_POSTE = 'idx_employee_profile_poste';

    public const INDEX_DEPARTEMENT = 'idx_employee_profile_departement';

    public const INDEX_CINEMA_ID = 'idx_employee_profile_cinema_id';

    // Foreign Keys
    public const FK_USER_DB_ID = 'fk_employee_profile_user_db_id';

    public const FK_USER_UUID = 'fk_employee_profile_user_uuid';

    public const FK_RESPONSABLE_UUID = 'fk_employee_profile_responsable_uuid';

    public const FK_CINEMA_ID = 'fk_employee_profile_cinema_id';

    // Constraints
    public const UNIQUE_USER_DB_ID = 'uniq_employee_profile_user_db_id';

    public const UNIQUE_USER_UUID = 'uniq_employee_profile_user_uuid';

    public const UNIQUE_EMAIL = 'uniq_employee_profile_email';

    public const UNIQUE_NUMERO_EMPLOYE = 'uniq_employee_profile_numero_employe';
}
