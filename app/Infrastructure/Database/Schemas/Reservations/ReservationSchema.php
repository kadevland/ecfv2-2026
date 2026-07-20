<?php

declare(strict_types=1);

namespace App\Infrastructure\Database\Schemas\Reservations;

use App\Infrastructure\Database\Schemas\DatabaseSchemas;

/**
 * Schema constants for reservations table
 * Réservations clients avec gestion statuts et système dual-key
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

    public const NUMERO_RESERVATION = 'numero_reservation';

    // Foreign Keys vers User
    public const USER_KEY = 'user_db_id';

    public const USER_ID = 'user_uuid';

    // Foreign Keys vers Seance
    public const SEANCE_KEY = 'seance_db_id';

    public const SEANCE_ID = 'seance_uuid';

    public const NOMBRE_PLACES = 'nombre_places';

    public const PLACES_DETAILS = 'places_details';

    public const DETAILS_PLACES = 'details_places';

    public const MONTANT_TOTAL = 'montant_total';

    public const MONTANT_TOTAL_DEVISE = 'montant_total_devise';

    public const MONTANT_HT = 'montant_ht';

    public const MONTANT_HT_DEVISE = 'montant_ht_devise';

    public const TAUX_TVA = 'taux_tva';

    public const STATUT = 'statut';

    public const PRIX_UNITAIRE_HT_CENTIMES = 'prix_unitaire_ht_centimes';

    public const PRIX_TOTAL_HT_CENTIMES = 'prix_total_ht_centimes';

    public const PRIX_TOTAL_TTC_CENTIMES = 'prix_total_ttc_centimes';

    public const TAUX_TVA_BASIS_POINTS = 'taux_tva_basis_points';

    public const DEVISE = 'devise';

    public const DATE_RESERVATION = 'date_reservation';

    public const DATE_EXPIRATION = 'date_expiration';

    public const DATE_CONFIRMATION = 'date_confirmation';

    public const DATE_PAIEMENT = 'date_paiement';

    public const DATE_UTILISATION = 'date_utilisation';

    public const COMMENTAIRES = 'commentaires';

    public const EMAIL_CONFIRMATION = 'email_confirmation';

    public const TELEPHONE_CONTACT = 'telephone_contact';

    public const METADONNEES_PAIEMENT = 'metadonnees_paiement';

    public const CODE_CONFIRMATION = 'code_confirmation';

    public const TOKEN_SECURITE = 'token_securite';

    public const CANAL_RESERVATION = 'canal_reservation';

    public const CODE_PROMOTION = 'code_promotion';

    public const REMISE_CENTIMES = 'remise_centimes';

    public const IP_RESERVATION = 'ip_reservation';

    public const USER_AGENT = 'user_agent';

    public const METADONNEES_RESERVATION = 'metadonnees_reservation';

    public const NOTES_CLIENT = 'notes_client';

    public const NOTES_INTERNES = 'notes_internes';

    public const QR_CODE = 'qr_code';

    public const CREATED_AT = 'created_at';

    public const UPDATED_AT = 'updated_at';

    // JSONB Places details fields
    public const TYPE_PLACEMENT = 'type_placement';

    public const PLACES = 'places';

    public const RANGEE = 'rangee';

    public const NUMERO_PLACE = 'numero';

    public const TYPE_TARIF = 'type_tarif';

    public const PRIX_UNITAIRE = 'prix_unitaire';

    public const MODULATIONS_APPLIQUEES = 'modulations_appliquees';

    // Indexes
    public const INDEX_UUID = 'idx_reservation_uuid';

    public const INDEX_NUMERO = 'idx_reservation_numero';

    public const INDEX_USER = 'idx_reservation_user';

    public const INDEX_NUMERO_RESERVATION = 'idx_reservation_numero';

    public const INDEX_USER_DB_ID = 'idx_reservation_user_db_id';

    public const INDEX_USER_UUID = 'idx_reservation_user_uuid';

    public const INDEX_SEANCE = 'idx_reservation_seance';

    public const INDEX_SEANCE_ID = 'idx_reservation_seance';

    public const INDEX_DATE_RESERVATION = 'idx_reservation_date_reservation';

    public const INDEX_EMAIL = 'idx_reservation_email';

    public const INDEX_STATUT = 'idx_reservation_statut';

    public const INDEX_DATE_EXPIRATION = 'idx_reservation_expiration';

    public const INDEX_QR_CODE = 'idx_reservation_qr';

    public const INDEX_CODE_CONFIRMATION = 'idx_reservation_code_confirmation';

    public const INDEX_USER_STATUT = 'idx_reservation_user_statut';

    public const INDEX_SEANCE_STATUT = 'idx_reservation_seance_statut';

    public const INDEX_DATE_STATUT = 'idx_reservation_date_statut';

    public const INDEX_PLACES_DETAILS = 'idx_reservation_places'; // GIN index sur JSONB

    public const INDEX_DETAILS_PLACES = 'idx_reservation_details_places';

    public const INDEX_METADONNEES_RESERVATION = 'idx_reservation_metadonnees_reservation';

    public const INDEX_METADONNEES_PAIEMENT = 'idx_reservation_metadonnees_paiement';

    public const INDEX_METADONNEES = 'idx_reservation_metadonnees';

    public const INDEX_CREATED_AT = 'idx_reservation_created_at';

    // Foreign Keys
    public const FK_USER_DB_ID = 'fk_reservation_user_db_id';

    public const FK_USER_UUID = 'fk_reservation_user_uuid';

    public const FK_SEANCE_ID = 'fk_reservation_seance';

    // Constraints
    public const CONSTRAINT_STATUT_CHECK = 'chk_reservation_statut';

    public const CONSTRAINT_PLACES_CHECK = 'chk_reservation_places';

    public const CONSTRAINT_MONTANTS_CHECK = 'chk_reservation_montants';

    public const CONSTRAINT_USER_TYPE_CHECK = 'chk_reservation_user_type';

    public const UNIQUE_NUMERO = 'uniq_reservation_numero';
}
