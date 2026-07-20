<?php

declare(strict_types=1);

namespace App\Infrastructure\Database\Schemas\Cinema;

use App\Infrastructure\Database\Schemas\DatabaseSchemas;

final class CinemaSchema
{
    public const SCHEMA = DatabaseSchemas::CINEMA;

    // Table configuration
    public const TABLE = 'cinemas';

    public const FULL_TABLE = self::SCHEMA . '.' . self::TABLE;

    public const CONNECTION = 'pgsql';

    // Primary Keys
    public const PRIMARY_KEY = 'db_id';    // PK technique PostgreSQL auto-increment

    // Domain identifier (business) - pattern DDD correct
    public const ID = 'uuid';              // UUID métier/domain (UUID v7)

    // Business Columns
    public const NOM = 'nom';

    public const PAYS = 'pays';

    public const ADRESSE = 'adresse'; // JSONB contenant rue, ville, code_postal, complement

    // JSONB Address fields
    public const ADRESSE_RUE = 'rue';

    public const ADRESSE_VILLE = 'ville';

    public const ADRESSE_CODE_POSTAL = 'code_postal';

    public const ADRESSE_COMPLEMENT = 'complement';

    public const TELEPHONE = 'telephone';

    public const EMAIL = 'email';

    public const EST_ACTIF = 'est_actif';

    public const DESCRIPTION = 'description';

    public const COORDONNEES_GPS = 'coordonnees_gps'; // JSONB contenant latitude, longitude

    // JSONB GPS fields
    public const GPS_LATITUDE = 'latitude';

    public const GPS_LONGITUDE = 'longitude';

    public const HORAIRES_OUVERTURE = 'horaires_ouverture'; // JSONB contenant 7 jours d'horaires

    public const CREATED_AT = 'created_at';

    public const UPDATED_AT = 'updated_at';

    // Indexes
    public const INDEX_UUID = 'idx_cinemas_uuid';

    public const INDEX_PAYS = 'idx_cinemas_pays';

    public const INDEX_ADRESSE = 'idx_cinemas_adresse'; // GIN index sur JSONB

    public const INDEX_COORDONNEES_GPS = 'idx_cinemas_coordonnees_gps'; // GIN index sur JSONB GPS

    // Constraints
    public const UNIQUE_NOM_PAYS = 'cinemas_nom_pays_unique';
}
