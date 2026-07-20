<?php

declare(strict_types=1);

namespace App\Infrastructure\Database\Schemas\Employees;

use App\Infrastructure\Database\Schemas\DatabaseSchemas;

/**
 * Schema constants for emplois table
 * Définition des postes et emplois avec salaires et compétences
 */
final class EmploiSchema
{
    // Table configuration
    public const SCHEMA = DatabaseSchemas::EMPLOYEES;

    public const TABLE = 'emplois';

    public const FULL_TABLE = self::SCHEMA . '.' . self::TABLE;

    public const PRIMARY_KEY = 'db_id';

    public const CONNECTION = 'pgsql';

    // Columns
    public const ID = 'uuid';

    // Foreign Keys vers UserProfil (dual FK pattern)
    public const USER_PROFIL_KEY = 'user_profil_db_id';

    public const USER_PROFIL_ID = 'user_profil_uuid';

    // Foreign Keys vers Cinema
    public const CINEMA_KEY = 'cinema_db_id';

    public const CINEMA_ID = 'cinema_uuid';

    // Additional fields from Emploi model
    public const POSTE = 'poste';

    public const NIVEAU_ACCES = 'niveau_acces';

    public const MANAGER_DB_ID = 'manager_db_id';

    public const MANAGER_UUID = 'manager_uuid';

    public const DATE_DEBUT = 'date_debut';

    public const DATE_FIN = 'date_fin';

    public const DATE_EMBAUCHE = 'date_embauche';

    public const EST_ACTIF = 'est_actif';

    public const RAISON_FIN = 'raison_fin';

    public const TITRE_POSTE = 'titre_poste';

    public const DESCRIPTION = 'description';

    public const CATEGORIE = 'categorie';

    public const NIVEAU = 'niveau';

    public const TYPE_CONTRAT = 'type_contrat';

    public const TEMPS_TRAVAIL = 'temps_travail';

    public const SALAIRE_MIN_HT_CENTIMES = 'salaire_min_ht_centimes';

    public const SALAIRE_MAX_HT_CENTIMES = 'salaire_max_ht_centimes';

    public const DEVISE = 'devise';

    public const PERIODICITE_SALAIRE = 'periodicite_salaire';

    public const AVANTAGES = 'avantages';

    public const COMPETENCES_REQUISES = 'competences_requises';

    public const COMPETENCES_SOUHAITEES = 'competences_souhaitees';

    public const FORMATIONS_REQUISES = 'formations_requises';

    public const EXPERIENCE_MINIMUM_MOIS = 'experience_minimum_mois';

    public const HEURE_DEBUT_TYPE = 'heure_debut_type';

    public const HEURE_FIN_TYPE = 'heure_fin_type';

    public const JOURS_TRAVAIL = 'jours_travail';

    public const TRAVAIL_WEEKEND = 'travail_weekend';

    public const TRAVAIL_FERIES = 'travail_feries';

    public const TRAVAIL_SOIREE = 'travail_soiree';

    public const RESPONSABILITES = 'responsabilites';

    public const ENCADREMENT_EQUIPE = 'encadrement_equipe';

    public const NOMBRE_PERSONNES_ENCADREES = 'nombre_personnes_encadrees';

    public const RESPONSABLE_HIERARCHIQUE_ID = 'responsable_hierarchique_id';

    public const STATUT = 'statut';

    public const RECRUTEMENT_OUVERT = 'recrutement_ouvert';

    public const DATE_CREATION_POSTE = 'date_creation_poste';

    public const DATE_FERMETURE_POSTE = 'date_fermeture_poste';

    public const CODE_POSTE = 'code_poste';

    public const CLASSIFICATION_CONVENTION = 'classification_convention';

    public const NOTES_RH = 'notes_rh';

    public const CREATED_AT = 'created_at';

    public const UPDATED_AT = 'updated_at';

    // Indexes
    public const INDEX_UUID = 'idx_emploi_uuid';

    public const INDEX_CINEMA = 'idx_emploi_cinema';

    public const INDEX_TITRE = 'idx_emploi_titre';

    public const INDEX_CATEGORIE = 'idx_emploi_categorie';

    public const INDEX_NIVEAU = 'idx_emploi_niveau';

    public const INDEX_TYPE_CONTRAT = 'idx_emploi_type_contrat';

    public const INDEX_STATUT = 'idx_emploi_statut';

    public const INDEX_RECRUTEMENT = 'idx_emploi_recrutement';

    public const INDEX_CINEMA_STATUT = 'idx_emploi_cinema_statut';

    public const INDEX_CATEGORIE_RECRUTEMENT = 'idx_emploi_categorie_recrutement';

    public const INDEX_CONTRAT_STATUT = 'idx_emploi_contrat_statut';

    public const INDEX_COMPETENCES_REQUISES = 'idx_emploi_competences_requises';

    public const INDEX_COMPETENCES_SOUHAITEES = 'idx_emploi_competences_souhaitees';

    public const INDEX_AVANTAGES = 'idx_emploi_avantages';

    public const INDEX_JOURS_TRAVAIL = 'idx_emploi_jours_travail';

    // Foreign Keys
    public const FK_CINEMA_DB_ID = 'fk_emploi_cinema_db_id';

    public const FK_CINEMA_UUID = 'fk_emploi_cinema_uuid';

    public const FK_RESPONSABLE_HIERARCHIQUE_ID = 'fk_emploi_responsable_hierarchique_id';

    // Constraints
    public const CHECK_SALAIRE_MIN_POSITIF = 'chk_emploi_salaire_min_positif';

    public const CHECK_SALAIRE_MAX_POSITIF = 'chk_emploi_salaire_max_positif';

    public const CHECK_SALAIRES_COHERENTS = 'chk_emploi_salaires_coherents';

    public const CHECK_EXPERIENCE_POSITIVE = 'chk_emploi_experience_positive';

    public const CHECK_ENCADREMENT_POSITIF = 'chk_emploi_encadrement_positif';

    public const UNIQUE_CODE_POSTE = 'uniq_emploi_code_poste';
}
