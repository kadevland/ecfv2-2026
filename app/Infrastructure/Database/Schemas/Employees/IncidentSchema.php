<?php

declare(strict_types=1);

namespace App\Infrastructure\Database\Schemas\Employees;

use App\Infrastructure\Database\Schemas\DatabaseSchemas;

/**
 * Schema constants for incidents table
 * Incidents techniques pour application desktop (US15)
 */
final class IncidentSchema
{
    // Table configuration
    public const SCHEMA = DatabaseSchemas::EMPLOYEES;

    public const TABLE = 'incidents';

    public const FULL_TABLE = self::SCHEMA . '.' . self::TABLE;

    public const PRIMARY_KEY = 'db_id';

    public const CONNECTION = 'pgsql';

    // Columns

    public const ID = 'uuid';

    public const NUMERO_INCIDENT = 'numero_incident';

    public const CONTRAT_RAPPORTEUR_ID = 'contrat_rapporteur_id';

    public const CINEMA_DB_ID = 'cinema_db_id';

    public const CINEMA_UUID = 'cinema_uuid';

    public const SALLE_DB_ID = 'salle_db_id';

    public const SEANCE_DB_ID = 'seance_db_id';

    public const TYPE_INCIDENT = 'type_incident';

    public const CATEGORIE = 'categorie';

    public const NIVEAU_GRAVITE = 'niveau_gravite';

    public const NIVEAU_PRIORITE = 'niveau_priorite';

    public const DATE_INCIDENT = 'date_incident';

    public const DATE_RAPPORT = 'date_rapport';

    public const DATE_PRISE_EN_COMPTE = 'date_prise_en_compte';

    public const DATE_RESOLUTION = 'date_resolution';

    public const TITRE = 'titre';

    public const DESCRIPTION = 'description';

    public const ACTIONS_IMMEDIATES = 'actions_immediates';

    public const CONSEQUENCES = 'consequences';

    public const PERSONNES_IMPLIQUEES = 'personnes_impliquees';

    public const TEMOINS = 'temoins';

    public const DEGATS_MATERIELS = 'degats_materiels';

    public const COUT_DEGATS_CENTIMES = 'cout_degats_centimes';

    public const DEVISE = 'devise';

    public const ASSURANCE_IMPLIQUEE = 'assurance_impliquee';

    public const NUMERO_SINISTRE = 'numero_sinistre';

    public const STATUT = 'statut';

    public const ASSIGNE_A_CONTRAT_ID = 'assigne_a_contrat_id';

    public const PLAN_ACTION = 'plan_action';

    public const RESOLUTION_FINALE = 'resolution_finale';

    public const CAUSES_RACINES = 'causes_racines';

    public const MESURES_PREVENTIVES = 'mesures_preventives';

    public const FORMATION_REQUISE = 'formation_requise';

    public const RECOMMANDATIONS = 'recommandations';

    public const DECLARATION_OBLIGATOIRE = 'declaration_obligatoire';

    public const DECLARATION_EFFECTUEE = 'declaration_effectuee';

    public const DATE_DECLARATION = 'date_declaration';

    public const ORGANISME_DECLARE = 'organisme_declare';

    public const PHOTOS_URLS = 'photos_urls';

    public const DOCUMENTS_URLS = 'documents_urls';

    public const VIDEOS_URLS = 'videos_urls';

    public const DEVICE_ID = 'device_id';

    public const APP_VERSION = 'app_version';

    public const IP_SAISIE = 'ip_saisie';

    public const METADONNEES_TECHNIQUE = 'metadonnees_technique';

    public const HISTORIQUE_WORKFLOW = 'historique_workflow';

    public const NOTIFICATIONS_ENVOYEES = 'notifications_envoyees';

    public const NOTES_COMPLEMENTAIRES = 'notes_complementaires';

    public const CREATED_AT = 'created_at';

    public const UPDATED_AT = 'updated_at';

    // Indexes
    public const INDEX_UUID = 'idx_incident_uuid';

    public const INDEX_NUMERO = 'idx_incident_numero';

    public const INDEX_RAPPORTEUR = 'idx_incident_rapporteur';

    public const INDEX_CINEMA = 'idx_incident_cinema';

    public const INDEX_TYPE = 'idx_incident_type';

    public const INDEX_GRAVITE = 'idx_incident_gravite';

    public const INDEX_PRIORITE = 'idx_incident_priorite';

    public const INDEX_STATUT = 'idx_incident_statut';

    public const INDEX_DATE_INCIDENT = 'idx_incident_date_incident';

    public const INDEX_DATE_RAPPORT = 'idx_incident_date_rapport';

    public const INDEX_CINEMA_STATUT = 'idx_incident_cinema_statut';

    public const INDEX_TYPE_GRAVITE = 'idx_incident_type_gravite';

    public const INDEX_DATE_STATUT = 'idx_incident_date_statut';

    public const INDEX_ASSIGNE_STATUT = 'idx_incident_assigne_statut';

    public const INDEX_PERSONNES_IMPLIQUEES = 'idx_incident_personnes_impliquees';

    public const INDEX_TEMOINS = 'idx_incident_temoins';

    public const INDEX_PHOTOS = 'idx_incident_photos';

    public const INDEX_DOCUMENTS = 'idx_incident_documents';

    public const INDEX_METADONNEES_TECHNIQUE = 'idx_incident_metadonnees_technique';

    public const INDEX_HISTORIQUE_WORKFLOW = 'idx_incident_historique_workflow';

    // Foreign Keys
    public const FK_CONTRAT_RAPPORTEUR = 'fk_incident_contrat_rapporteur';

    public const FK_CINEMA_DB_ID = 'fk_incident_cinema_db_id';

    public const FK_CINEMA_UUID = 'fk_incident_cinema_uuid';

    public const FK_SALLE_DB_ID = 'fk_incident_salle_db_id';

    public const FK_SEANCE_DB_ID = 'fk_incident_seance_db_id';

    public const FK_ASSIGNE_A_CONTRAT = 'fk_incident_assigne_a_contrat';

    // Constraints
    public const CHECK_DATES_RAPPORT_COHERENTES = 'chk_incident_dates_rapport_coherentes';

    public const CHECK_DATES_PRISE_EN_COMPTE_COHERENTES = 'chk_incident_dates_prise_en_compte_coherentes';

    public const CHECK_DATES_RESOLUTION_COHERENTES = 'chk_incident_dates_resolution_coherentes';

    public const CHECK_COUT_POSITIF = 'chk_incident_cout_positif';

    public const UNIQUE_NUMERO_INCIDENT = 'uniq_incident_numero';
}
