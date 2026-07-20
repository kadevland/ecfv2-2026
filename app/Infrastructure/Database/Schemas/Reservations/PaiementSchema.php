<?php

declare(strict_types=1);

namespace App\Infrastructure\Database\Schemas\Reservations;

use App\Infrastructure\Database\Schemas\DatabaseSchemas;

/**
 * Schema constants for paiements table
 * Gestion séparée des paiements pour audit et réconciliation comptable
 */
final class PaiementSchema
{
    // Table configuration
    public const SCHEMA = DatabaseSchemas::RESERVATIONS;

    public const TABLE = 'paiements';

    public const FULL_TABLE = self::SCHEMA . '.' . self::TABLE;

    public const PRIMARY_KEY = 'db_id';

    public const CONNECTION = 'pgsql';

    // Columns
    public const ID = 'uuid';

    // Foreign Keys vers Reservation
    public const RESERVATION_KEY = 'reservation_db_id';

    public const RESERVATION_ID = 'reservation_uuid';

    public const NUMERO_TRANSACTION = 'numero_transaction';

    public const MONTANT_HT_CENTIMES = 'montant_ht_centimes';

    public const DEVISE = 'devise';

    public const TAUX_TVA_BASIS_POINTS = 'taux_tva_basis_points';

    public const MONTANT_TTC_CENTIMES = 'montant_ttc_centimes';

    public const METHODE_PAIEMENT = 'methode_paiement';

    public const STATUT = 'statut';

    public const STATUT_PAIEMENT = 'statut_paiement';

    public const MONTANT_DEMANDE = 'montant_demande';

    public const MONTANT_DEMANDE_DEVISE = 'montant_demande_devise';

    public const MONTANT_PAYE = 'montant_paye';

    public const MONTANT_PAYE_DEVISE = 'montant_paye_devise';

    public const FRAIS_TRANSACTION = 'frais_transaction';

    public const FRAIS_TRANSACTION_DEVISE = 'frais_transaction_devise';

    public const REFERENCE_EXTERNE = 'reference_externe';

    public const REFERENCE_BANQUE = 'reference_banque';

    public const PROCESSEUR_PAIEMENT = 'processeur_paiement';

    public const DATE_PAIEMENT = 'date_paiement';

    public const DATE_DEMANDE = 'date_demande';

    public const DATE_AUTORISATION = 'date_autorisation';

    public const DATE_CAPTURE = 'date_capture';

    public const DATE_EXPIRATION = 'date_expiration';

    public const DATE_REMBOURSEMENT = 'date_remboursement';

    public const CARTE_MASQUEE = 'carte_masquee';

    public const CARTE_TYPE = 'carte_type';

    public const CARTE_TOKEN = 'carte_token';

    public const CODE_AUTORISATION = 'code_autorisation';

    public const CODE_REPONSE = 'code_reponse';

    public const MESSAGE_REPONSE = 'message_reponse';

    public const SCORE_FRAUDE = 'score_fraude';

    public const RESULTAT_3DS = 'resultat_3ds';

    public const REFERENCE_3DS = 'reference_3ds';

    public const MONTANT_REMBOURSE_CENTIMES = 'montant_rembourse_centimes';

    public const RAISON_REMBOURSEMENT = 'raison_remboursement';

    public const IP_PAIEMENT = 'ip_paiement';

    public const USER_AGENT = 'user_agent';

    public const DONNEES_PSP = 'donnees_psp';

    public const DONNEES_PAIEMENT = 'donnees_paiement';

    public const IP_ADDRESS = 'ip_address';

    public const METADONNEES_PAIEMENT = 'metadonnees_paiement';

    public const NOMBRE_TENTATIVES = 'nombre_tentatives';

    public const DATE_DERNIERE_TENTATIVE = 'date_derniere_tentative';

    public const HISTORIQUE_ERREURS = 'historique_erreurs';

    public const NOTES_INTERNES = 'notes_internes';

    public const CREATED_AT = 'created_at';

    public const UPDATED_AT = 'updated_at';

    // Indexes
    public const INDEX_UUID = 'idx_paiement_uuid';

    public const INDEX_NUMERO_TRANSACTION = 'idx_paiement_numero_transaction';

    public const INDEX_RESERVATION = 'idx_paiement_reservation';

    public const INDEX_REFERENCE_EXTERNE = 'idx_paiement_reference_externe';

    public const INDEX_STATUT = 'idx_paiement_statut';

    public const INDEX_METHODE = 'idx_paiement_methode';

    public const INDEX_DATE_PAIEMENT = 'idx_paiement_date_paiement';

    public const INDEX_PROCESSEUR = 'idx_paiement_processeur';

    public const INDEX_RESERVATION_STATUT = 'idx_paiement_reservation_statut';

    public const INDEX_DATE_STATUT = 'idx_paiement_date_statut';

    public const INDEX_METHODE_STATUT = 'idx_paiement_methode_statut';

    public const INDEX_DONNEES_PSP = 'idx_paiement_donnees_psp';

    public const INDEX_METADONNEES_PAIEMENT = 'idx_paiement_metadonnees_paiement';

    // Foreign Keys
    public const FK_RESERVATION_DB_ID = 'fk_paiement_reservation_db_id';

    public const FK_RESERVATION_UUID = 'fk_paiement_reservation_uuid';

    // Constraints
    public const CHECK_MONTANT_POSITIF = 'chk_paiement_montant_positif';

    public const CHECK_REMBOURSEMENT_POSITIF = 'chk_paiement_remboursement_positif';

    public const CHECK_REMBOURSEMENT_COHERENT = 'chk_paiement_remboursement_coherent';

    public const CHECK_TENTATIVES_POSITIVES = 'chk_paiement_tentatives_positives';

    public const CHECK_SCORE_FRAUDE_VALIDE = 'chk_paiement_score_fraude_valide';

    public const UNIQUE_NUMERO_TRANSACTION = 'uniq_paiement_numero_transaction';
}
