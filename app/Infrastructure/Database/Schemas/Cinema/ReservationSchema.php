<?php

declare(strict_types=1);

namespace App\Infrastructure\Database\Schemas\Cinema;

use App\Infrastructure\Database\Schemas\DatabaseSchemas;

/**
 * Schema constants for reservations table
 * Réservations de places pour les séances
 */
final class ReservationSchema
{
    // Table configuration
    public const SCHEMA = DatabaseSchemas::RESERVATIONS;

    public const TABLE = 'reservations';

    public const FULL_TABLE = self::SCHEMA . '.' . self::TABLE;

    public const PRIMARY_KEY = 'db_id';

    public const CONNECTION = 'pgsql';

    // Columns
    public const ID = 'uuid';

    // Foreign Keys vers Seance
    public const SEANCE_KEY = 'seance_db_id';

    public const SEANCE_ID = 'seance_uuid';

    // Foreign Keys vers Utilisateur
    public const UTILISATEUR_KEY = 'user_db_id';

    public const UTILISATEUR_ID = 'user_uuid';

    // Domain model columns
    public const NOMBRE_PLACES = 'nombre_places';

    //public const MONTANT_TOTAL = 'montant_total';
    public const MONTANT_TOTAL = 'prix_total_ht_centimes';

    public const DEVISE = 'devise';

    public const STATUT = 'statut';

    public const DATE_RESERVATION = 'date_reservation';

    public const DATE_EXPIRATION = 'date_expiration';

    public const DETAILS_BILLETS = 'details_billets';

    public const INFORMATIONS_PAIEMENT = 'informations_paiement';

    // Timestamps
    public const CREATED_AT = 'created_at';

    public const UPDATED_AT = 'updated_at';
}
