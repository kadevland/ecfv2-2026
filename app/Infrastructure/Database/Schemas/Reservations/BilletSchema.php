<?php

declare(strict_types=1);

namespace App\Infrastructure\Database\Schemas\Reservations;

use App\Infrastructure\Database\Schemas\DatabaseSchemas;

/**
 * Schema constants for billets table
 * Billets individuels avec QR codes et gestion places
 */
final class BilletSchema
{
    // Table configuration
    public const SCHEMA = DatabaseSchemas::RESERVATIONS;

    public const TABLE = 'billets';

    public const FULL_TABLE = self::SCHEMA . '.' . self::TABLE;

    public const PRIMARY_KEY = 'db_id';

    public const CONNECTION = 'pgsql';

    // Columns
    public const ID = 'uuid';

    public const NUMERO_BILLET = 'numero_billet';

    // Foreign Keys vers Reservation
    public const RESERVATION_KEY = 'reservation_db_id';

    public const RESERVATION_ID = 'reservation_uuid';

    // Foreign Keys vers Seance
    public const SEANCE_KEY = 'seance_db_id';

    public const SEANCE_ID = 'seance_uuid';

    public const PLACEMENT_LIBRE = 'placement_libre';

    public const RANGEE = 'rangee';

    public const NUMERO_PLACE = 'numero_place';

    public const TYPE_PLACE = 'type_place';

    public const NUMERO_SIEGE = 'numero_siege';

    public const TYPE_TARIF = 'type_tarif';

    public const PRIX_UNITAIRE = 'prix_unitaire';

    public const PRIX_UNITAIRE_DEVISE = 'prix_unitaire_devise';

    public const PRIX_HT_CENTIMES = 'prix_ht_centimes';

    public const DEVISE = 'devise';

    public const TAUX_TVA_BASIS_POINTS = 'taux_tva_basis_points';

    public const PRIX_TTC_CENTIMES = 'prix_ttc_centimes';

    public const STATUT = 'statut';

    public const STATUT_BILLET = 'statut_billet';

    public const QR_CODE_DATA = 'qr_code_data';

    public const QR_CODE_URL = 'qr_code_url';

    public const QR_CODE_HASH = 'qr_code_hash';

    public const QR_CODE_INDIVIDUEL = 'qr_code_individuel';

    public const DATE_EMISSION = 'date_emission';

    public const DATE_ENVOI = 'date_envoi';

    public const DATE_VALIDATION = 'date_validation';

    public const DATE_UTILISATION = 'date_utilisation';

    public const CODE_VALIDATION = 'code_validation';

    public const EMPLOYE_VALIDATION = 'employe_validation';

    public const CANAL_VALIDATION = 'canal_validation';

    public const METADONNEES_VALIDATION = 'metadonnees_validation';

    public const TERMINAL_VALIDATION = 'terminal_validation';

    public const MODE_LIVRAISON = 'mode_livraison';

    public const ADRESSE_LIVRAISON = 'adresse_livraison';

    public const DATE_DERNIERE_TENTATIVE_ENVOI = 'date_derniere_tentative_envoi';

    public const NOMBRE_TENTATIVES_ENVOI = 'nombre_tentatives_envoi';

    public const FORMAT_QR = 'format_qr';

    public const TAILLE_QR_PIXELS = 'taille_qr_pixels';

    public const NIVEAU_CORRECTION_QR = 'niveau_correction_qr';

    public const SIGNATURE_NUMERIQUE = 'signature_numerique';

    public const DATE_EXPIRATION_QR = 'date_expiration_qr';

    public const NOMBRE_SCANS = 'nombre_scans';

    public const HISTORIQUE_SCANS = 'historique_scans';

    public const METADONNEES_IMPRESSION = 'metadonnees_impression';

    public const NOTES = 'notes';

    public const CONTROLE_PAR = 'controle_par';

    public const CREATED_AT = 'created_at';

    public const UPDATED_AT = 'updated_at';

    // Indexes
    public const INDEX_UUID = 'idx_billet_uuid';

    public const INDEX_NUMERO = 'idx_billet_numero';

    public const INDEX_RESERVATION = 'idx_billet_reservation';

    public const INDEX_QR_HASH = 'idx_billet_qr_hash';

    public const INDEX_CODE_VALIDATION = 'idx_billet_code_validation';

    public const INDEX_DATE_EMISSION = 'idx_billet_date_emission';

    public const INDEX_DATE_VALIDATION = 'idx_billet_date_validation';

    public const INDEX_RESERVATION_STATUT = 'idx_billet_reservation_statut';

    public const INDEX_PLACE = 'idx_billet_place';

    public const INDEX_DATE_STATUT = 'idx_billet_date_statut';

    public const INDEX_HISTORIQUE_SCANS = 'idx_billet_historique_scans';

    public const INDEX_METADONNEES_IMPRESSION = 'idx_billet_metadonnees_impression';

    public const INDEX_NUMERO_BILLET = 'idx_billet_numero';

    public const INDEX_RESERVATION_ID = 'idx_billet_reservation';

    public const INDEX_SEANCE_ID = 'idx_billet_seance';

    public const INDEX_STATUT = 'idx_billet_statut';

    public const INDEX_PLACEMENT = 'idx_billet_placement';

    public const INDEX_QR_CODE = 'idx_billet_qr';

    public const INDEX_SIEGE = 'idx_billet_siege';

    public const INDEX_UTILISATION = 'idx_billet_utilisation';

    public const INDEX_SEANCE_PLACEMENT = 'idx_billet_seance_placement';

    // Foreign Keys
    public const FK_RESERVATION_ID = 'fk_billet_reservation';

    public const FK_SEANCE_ID = 'fk_billet_seance';

    public const FK_CONTROLE_PAR = 'fk_billet_controle_par';

    // Constraints
    public const CONSTRAINT_PLACEMENT_COHERENT = 'chk_billet_placement_coherent';

    public const CONSTRAINT_TARIF_CHECK = 'chk_billet_tarif';

    public const CONSTRAINT_STATUT_CHECK = 'chk_billet_statut';

    public const CONSTRAINT_UTILISATION_CHECK = 'chk_billet_utilisation';

    public const UNIQUE_NUMERO = 'uniq_billet_numero';

    public const UNIQUE_PLACE_RESERVATION = 'uniq_billet_place_reservation';

    public const CHECK_NUMERO_PLACE_POSITIF = 'chk_billet_numero_place_positif';

    public const CHECK_PRIX_POSITIF = 'chk_billet_prix_positif';

    public const CHECK_TENTATIVES_POSITIVES = 'chk_billet_tentatives_positives';

    public const CHECK_SCANS_POSITIFS = 'chk_billet_scans_positifs';

    public const CHECK_TAILLE_QR_POSITIVE = 'chk_billet_taille_qr_positive';

    public const UNIQUE_SEANCE_SIEGE = 'idx_billet_seance_siege_unique';
}
