<?php

declare(strict_types=1);

namespace App\Infrastructure\Database\Schemas\Profiles;

use App\Domain\Shared\Enums\SexeEnum;
use App\Infrastructure\Database\Schemas\DatabaseSchemas;

/**
 * Schema constants for user_profil table
 * Profils génériques pour tous utilisateurs (données personnelles uniquement)
 */
final class UserProfilSchema
{
    // Table configuration
    public const SCHEMA = DatabaseSchemas::PROFILES;

    public const TABLE = 'user_profil';

    public const FULL_TABLE = self::SCHEMA . '.' . self::TABLE;

    public const PRIMARY_KEY = 'db_id';

    public const CONNECTION = 'pgsql';

    // Columns
    public const ID = 'uuid';

    // Foreign Keys vers User
    public const USER_KEY = 'user_db_id'; // FK technique vers users.db_id

    public const USER_ID = 'user_uuid';   // FK business vers users.uuid

    // User type (duplicated for CQRS read-side optimization)
    public const TYPE = 'type';

    public const PRENOM = 'prenom';

    public const NOM = 'nom';

    public const DATE_NAISSANCE = 'date_naissance';

    public const EMAIL = 'email';

    public const TELEPHONE = 'telephone';

    public const SEXE = 'sexe';

    // Sexe enum values (utilise SexeEnum::values() pour les valeurs réelles)
    public const SEXE_MASCULIN = SexeEnum::MASCULIN->value;

    public const SEXE_FEMININ = SexeEnum::FEMININ->value;

    public const SEXE_AUTRE = SexeEnum::AUTRE->value;

    public const SEXE_NON_SPECIFIE = SexeEnum::NON_SPECIFIE->value;

    public const ADRESSE = 'adresse'; // JSONB: {rue, ville, code_postal, pays}

    public const CODE_POSTAL = 'code_postal';

    public const VILLE = 'ville';

    public const PAYS = 'pays';

    public const PREFERENCES = 'preferences'; // JSONB: preferences par namespace

    public const NEWSLETTER = 'newsletter';

    public const CREATED_AT = 'created_at';

    public const UPDATED_AT = 'updated_at';

    // JSONB Address fields
    public const ADRESSE_RUE = 'rue';

    public const ADRESSE_VILLE = 'ville';

    public const ADRESSE_CODE_POSTAL = 'code_postal';

    public const ADRESSE_PAYS = 'pays';

    // Indexes
    public const INDEX_UUID = 'idx_user_profil_uuid';

    public const INDEX_USER = 'idx_user_profil_user';

    public const INDEX_NOM_PRENOM = 'idx_user_profil_nom_prenom';

    public const INDEX_ADRESSE = 'idx_user_profil_adresse'; // GIN index sur JSONB

    public const INDEX_TYPE_UUID = 'idx_user_profil_type_uuid'; // Composite index pour CQRS

    // Foreign Keys
    public const FK_USER_DB_ID = 'fk_profil_user_db_id';

    public const FK_USER_UUID = 'fk_profil_user_uuid';

    // Constraints
    public const CONSTRAINT_AGE_CHECK = 'chk_profil_age';
}
