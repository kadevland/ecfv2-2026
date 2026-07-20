<?php

declare(strict_types=1);

namespace App\Infrastructure\Database\Schemas\Employees;

use App\Infrastructure\Database\Schemas\DatabaseSchemas;

/**
 * Schema constants for planning table
 * Planning et horaires de travail des employés
 */
final class PlanningSchema
{
    // Table configuration
    public const SCHEMA = DatabaseSchemas::EMPLOYEES;

    public const TABLE = 'plannings';

    public const FULL_TABLE = self::SCHEMA . '.' . self::TABLE;

    public const PRIMARY_KEY = 'db_id';

    public const CONNECTION = 'pgsql';

    // Columns
    public const ID = 'uuid';

    // Foreign Keys vers Cinema
    public const CINEMA_KEY = 'cinema_db_id';

    public const CINEMA_ID = 'cinema_uuid';

    // Foreign Keys vers Contrat
    public const CONTRAT_KEY = 'contrat_db_id';

    public const CONTRAT_ID = 'contrat_uuid';

    public const ANNEE = 'annee';

    public const SEMAINE = 'semaine';

    public const DATE_PLANNING = 'date_planning';

    public const HEURE_DEBUT = 'heure_debut';

    public const HEURE_FIN = 'heure_fin';

    public const HEURES_TRAVAILLEES = 'heures_travaillees';

    public const HEURES_PAUSE = 'heures_pause';

    public const HEURES_SUPPLEMENTAIRES = 'heures_supplementaires';

    public const EST_REMPLACEMENT = 'est_remplacement';

    public const STATUT = 'statut';

    public const DATE_VALIDATION_EMPLOYE = 'date_validation_employe';

    public const DATE_VALIDATION_MANAGER = 'date_validation_manager';

    public const DATE_DERNIERE_MODIFICATION = 'date_derniere_modification';

    public const HISTORIQUE_MODIFICATIONS = 'historique_modifications';

    public const EMPLOI_ID = 'emploi_id';

    public const PAUSE_DUREE = 'pause_duree';

    public const TYPE_SERVICE = 'type_service';

    public const REMPLACANT_ID = 'remplacant_id';

    public const NOTES = 'notes';

    public const DATE_TRAVAIL = 'date_travail';

    public const SEMAINE_ANNEE = 'semaine_annee';

    public const PAUSE_DEBUT = 'pause_debut';

    public const PAUSE_FIN = 'pause_fin';

    public const POSTE_ASSIGNE = 'poste_assigne';

    public const ZONE_AFFECTATION = 'zone_affectation';

    public const REMPLACE_CONTRAT_ID = 'remplace_contrat_id';

    public const MOTIF_REMPLACEMENT = 'motif_remplacement';

    public const TYPE_ABSENCE = 'type_absence';

    public const JUSTIFICATIF_REQUIS = 'justificatif_requis';

    public const JUSTIFICATIF_FOURNI = 'justificatif_fourni';

    public const POINTAGE_ARRIVEE = 'pointage_arrivee';

    public const POINTAGE_DEPART = 'pointage_depart';

    public const POINTAGE_PAUSE_DEBUT = 'pointage_pause_debut';

    public const POINTAGE_PAUSE_FIN = 'pointage_pause_fin';

    public const VALIDE_EMPLOYE = 'valide_employe';

    public const VALIDE_MANAGER = 'valide_manager';

    public const VALIDATEUR_MANAGER_ID = 'validateur_manager_id';

    public const NOTES_EMPLOYE = 'notes_employe';

    public const NOTES_MANAGER = 'notes_manager';

    public const NOTES_RH = 'notes_rh';

    public const MODIFIE_PAR_USER_ID = 'modifie_par_user_id';

    public const JOUR_FERIE = 'jour_ferie';

    public const WEEK_END = 'week_end';

    public const PERIODE_SPECIALE = 'periode_speciale';

    public const TAUX_HORAIRE_BASE = 'taux_horaire_base';

    public const MAJORATION_COEFFICIENT = 'majoration_coefficient';

    public const CREATED_AT = 'created_at';

    public const UPDATED_AT = 'updated_at';

    // Indexes
    public const INDEX_UUID = 'idx_planning_uuid';

    public const INDEX_CONTRAT = 'idx_planning_contrat';

    public const INDEX_CINEMA = 'idx_planning_cinema';

    public const INDEX_DATE = 'idx_planning_date';

    public const INDEX_SEMAINE = 'idx_planning_semaine';

    public const INDEX_ANNEE = 'idx_planning_annee';

    public const INDEX_TYPE_SERVICE = 'idx_planning_type_service';

    public const INDEX_STATUT = 'idx_planning_statut';

    public const INDEX_REMPLACEMENT = 'idx_planning_remplacement';

    public const INDEX_CONTRAT_DATE = 'idx_planning_contrat_date';

    public const INDEX_CINEMA_DATE = 'idx_planning_cinema_date';

    public const INDEX_ANNEE_SEMAINE = 'idx_planning_annee_semaine';

    public const INDEX_DATE_SERVICE = 'idx_planning_date_service';

    public const INDEX_STATUT_DATE = 'idx_planning_statut_date';

    public const INDEX_EMPLOI_ID = 'idx_planning_emploi_id';

    public const INDEX_DATE_TRAVAIL = 'idx_planning_date_travail';

    public const INDEX_REMPLACANT_ID = 'idx_planning_remplacant_id';

    public const INDEX_EMPLOI_DATE = 'idx_planning_emploi_date';

    // Foreign Keys
    public const FK_EMPLOI_ID = 'fk_planning_emploi_id';

    public const FK_REMPLACANT_ID = 'fk_planning_remplacant_id';

    // Constraints
    public const UNIQUE_CONTRAT_DATE = 'uniq_planning_contrat_date';

    public const CHECK_HORAIRES_COHERENTS = 'chk_planning_horaires_coherents';

    public const CHECK_PAUSE_COHERENTE = 'chk_planning_pause_coherente';

    public const CHECK_HEURES_POSITIVES = 'chk_planning_heures_positives';

    public const CHECK_PAUSE_POSITIVE = 'chk_planning_pause_positive';

    public const CHECK_HEURES_SUP_POSITIVES = 'chk_planning_heures_sup_positives';

    public const CHECK_SEMAINE_VALIDE = 'chk_planning_semaine_valide';

    public const CHECK_MAJORATION_POSITIVE = 'chk_planning_majoration_positive';

    public const CONSTRAINT_TYPE_SERVICE_CHECK = 'chk_planning_type_service';

    public const CONSTRAINT_STATUT_CHECK = 'chk_planning_statut';

    public const CONSTRAINT_HORAIRES_CHECK = 'chk_planning_horaires';

    public const UNIQUE_EMPLOI_DATE_HEURE = 'uniq_planning_emploi_date_heure';
}
