<?php

declare(strict_types=1);

namespace App\Infrastructure\Database\Schemas\Employees;

use App\Infrastructure\Database\Schemas\DatabaseSchemas;

/**
 * Schema constants for contrats table
 * Informations contractuelles et salariales des employés
 */
final class ContratSchema
{
    // Table configuration
    public const SCHEMA = DatabaseSchemas::EMPLOYEES;

    public const TABLE = 'contrats';

    public const FULL_TABLE = self::SCHEMA . '.' . self::TABLE;

    public const PRIMARY_KEY = 'db_id';

    public const CONNECTION = 'pgsql';

    // Columns

    public const ID = 'uuid';

    public const NUMERO_CONTRAT = 'numero_contrat';

    public const USER_PROFIL_ID = 'user_profil_id';

    public const USER_UUID = 'user_uuid';

    public const EMPLOI_DB_ID = 'emploi_db_id';

    public const EMPLOI_UUID = 'emploi_uuid';

    public const TYPE_CONTRAT = 'type_contrat';

    public const STATUT = 'statut';

    public const DATE_DEBUT = 'date_debut';

    public const DATE_FIN = 'date_fin';

    public const DATE_SIGNATURE = 'date_signature';

    public const DATE_FIN_PERIODE_ESSAI = 'date_fin_periode_essai';

    public const TEMPS_TRAVAIL = 'temps_travail';

    public const HEURES_HEBDOMADAIRES = 'heures_hebdomadaires';

    public const JOURS_CONGES_ANNUELS = 'jours_conges_annuels';

    public const SALAIRE_BRUT_HT_CENTIMES = 'salaire_brut_ht_centimes';

    public const DEVISE = 'devise';

    public const PERIODICITE_SALAIRE = 'periodicite_salaire';

    public const PRIME_ANCIENNETE_CENTIMES = 'prime_anciennete_centimes';

    public const PRIME_PERFORMANCE_CENTIMES = 'prime_performance_centimes';

    public const AVANTAGES_NATURE_CENTIMES = 'avantages_nature_centimes';

    public const DETAIL_AVANTAGES = 'detail_avantages';

    public const CONVENTION_COLLECTIVE = 'convention_collective';

    public const CLASSIFICATION_POSTE = 'classification_poste';

    public const COEFFICIENT_HIERARCHIQUE = 'coefficient_hierarchique';

    public const HORAIRES_STANDARDS = 'horaires_standards';

    public const TRAVAIL_WEEKEND = 'travail_weekend';

    public const TRAVAIL_FERIES = 'travail_feries';

    public const TRAVAIL_NUIT = 'travail_nuit';

    public const PREAVIS_JOURS = 'preavis_jours';

    public const CLAUSE_NON_CONCURRENCE = 'clause_non_concurrence';

    public const CLAUSE_CONFIDENTIALITE = 'clause_confidentialite';

    public const CLAUSE_MOBILITE = 'clause_mobilite';

    public const BUDGET_FORMATION_CENTIMES = 'budget_formation_centimes';

    public const OBJECTIFS_POSTE = 'objectifs_poste';

    public const DATE_PROCHAINE_EVALUATION = 'date_prochaine_evaluation';

    public const NUMERO_SECURITE_SOCIALE = 'numero_securite_sociale';

    public const VERSION = 'version';

    public const CONTRAT_PARENT_ID = 'contrat_parent_id';

    public const MOTIF_MODIFICATION = 'motif_modification';

    public const DATE_FIN_EFFECTIVE = 'date_fin_effective';

    public const MOTIF_FIN = 'motif_fin';

    public const COMMENTAIRE_FIN = 'commentaire_fin';

    public const DOCUMENT_PDF_URL = 'document_pdf_url';

    public const DATE_SIGNATURE_EMPLOYE = 'date_signature_employe';

    public const DATE_SIGNATURE_EMPLOYEUR = 'date_signature_employeur';

    public const SIGNATURE_EMPLOYE_URL = 'signature_employe_url';

    public const SIGNATURE_EMPLOYEUR_URL = 'signature_employeur_url';

    public const METADONNEES_CONTRAT = 'metadonnees_contrat';

    public const NOTES_RH = 'notes_rh';

    public const CREATED_AT = 'created_at';

    public const UPDATED_AT = 'updated_at';

    // Indexes
    public const INDEX_UUID = 'idx_contrat_uuid';

    public const INDEX_NUMERO = 'idx_contrat_numero';

    public const INDEX_USER = 'idx_contrat_user';

    public const INDEX_EMPLOI = 'idx_contrat_emploi';

    public const INDEX_TYPE = 'idx_contrat_type';

    public const INDEX_STATUT = 'idx_contrat_statut';

    public const INDEX_DATE_DEBUT = 'idx_contrat_date_debut';

    public const INDEX_DATE_FIN = 'idx_contrat_date_fin';

    public const INDEX_VERSION = 'idx_contrat_version';

    public const INDEX_PERIODE = 'idx_contrat_periode';

    public const INDEX_USER_STATUT = 'idx_contrat_user_statut';

    public const INDEX_TYPE_STATUT = 'idx_contrat_type_statut';

    public const INDEX_DETAIL_AVANTAGES = 'idx_contrat_detail_avantages';

    public const INDEX_HORAIRES_STANDARDS = 'idx_contrat_horaires_standards';

    public const INDEX_METADONNEES_CONTRAT = 'idx_contrat_metadonnees_contrat';

    // Foreign Keys
    public const FK_USER_PROFIL_ID = 'fk_contrat_user_profil_id';

    public const FK_USER_UUID = 'fk_contrat_user_uuid';

    public const FK_EMPLOI_DB_ID = 'fk_contrat_emploi_db_id';

    public const FK_EMPLOI_UUID = 'fk_contrat_emploi_uuid';

    public const FK_CONTRAT_PARENT_ID = 'fk_contrat_contrat_parent_id';

    // Constraints
    public const CHECK_SALAIRE_POSITIF = 'chk_contrat_salaire_positif';

    public const CHECK_HEURES_POSITIVES = 'chk_contrat_heures_positives';

    public const CHECK_CONGES_POSITIFS = 'chk_contrat_conges_positifs';

    public const CHECK_PRIME_ANCIENNETE_POSITIVE = 'chk_contrat_prime_anciennete_positive';

    public const CHECK_VERSION_POSITIVE = 'chk_contrat_version_positive';

    public const CHECK_DATES_COHERENTES = 'chk_contrat_dates_coherentes';

    public const UNIQUE_NUMERO_CONTRAT = 'uniq_contrat_numero_contrat';

    public const UNIQUE_USER_EMPLOI_VERSION = 'uniq_contrat_user_emploi_version';
}
