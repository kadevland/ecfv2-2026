<?php

declare(strict_types=1);

namespace App\Infrastructure\Database\Schemas\Cinema;

use App\Infrastructure\Database\Schemas\DatabaseSchemas;

/**
 * Schema constants for seances table
 * Séances de projection avec tarification dynamique
 */
final class SeanceSchema
{
    // Table configuration
    public const SCHEMA = DatabaseSchemas::CINEMA;

    public const TABLE = 'seances';

    public const FULL_TABLE = self::SCHEMA . '.' . self::TABLE;

    public const PRIMARY_KEY = 'db_id';

    public const CONNECTION = 'pgsql';

    // Columns
    public const ID = 'uuid';

    // Foreign Keys vers Film
    public const FILM_KEY = 'film_db_id';

    public const FILM_ID = 'film_uuid';

    // Foreign Keys vers Salle
    public const SALLE_KEY = 'salle_db_id';

    public const SALLE_ID = 'salle_uuid';

    // Domain model columns (added by migration)
    public const DATE_HEURE_DEBUT = 'date_heure_debut';

    public const DATE_HEURE_FIN = 'date_heure_fin';

    public const DATE_SEANCE = 'date_seance';

    public const HEURE_DEBUT = 'heure_debut';

    public const HEURE_FIN = 'heure_fin';

    public const VERSION = 'version';

    public const EST_3D = 'est_3d';

    public const PRIX_HT_CENTIMES = 'prix_ht_centimes';

    public const TARIFICATION = 'tarification';

    public const TAUX_TVA = 'taux_tva';

    public const TAUX_TVA_BASIS_POINTS = 'taux_tva_basis_points';

    public const PRIX_TTC_CENTIMES = 'prix_ttc_centimes';

    public const DEVISE = 'devise';

    public const PLACEMENT_LIBRE = 'placement_libre';

    public const PLACES_DISPONIBLES = 'places_disponibles';

    public const PLACES_RESERVEES = 'places_reservees';

    public const PLACES_VENDUES = 'places_vendues';

    public const STATUT = 'statut';

    public const OUVERTURE_VENTE = 'ouverture_vente';

    public const FERMETURE_VENTE = 'fermeture_vente';

    public const SEANCE_SPECIALE = 'seance_speciale';

    public const TYPE_SEANCE = 'type_seance';

    public const NOTES = 'notes';

    public const CONFIGURATION_TECHNIQUE = 'configuration_technique';

    public const METADONNEES_COMMERCIALES = 'metadonnees_commerciales';

    public const OPTIONS_SUPPLEMENTAIRES = 'options_supplementaires';

    public const DUREE_ADDITIONNELLE = 'duree_additionnelle';

    public const QUALITE_PROJECTION = 'qualite_projection';

    public const QUALITE_SONORE = 'qualite_sonore';

    // Legacy columns (from original migration)
    public const PLACES_DISPONIBLES_OLD = 'places_disponibles';

    public const SEANCE_SPECIALE_OLD = 'seance_speciale';

    public const CREATED_AT = 'created_at';

    public const UPDATED_AT = 'updated_at';

    // JSONB Tarification fields
    public const TARIFS_BASE = 'tarifs_base';

    public const TARIFS_NORMAL = 'normal';

    public const TARIFS_REDUIT = 'reduit';

    public const TARIFS_SENIOR = 'senior';

    public const TARIFS_ENFANT = 'enfant';

    public const TARIFS_PMR = 'pmr';

    public const MODULATIONS = 'modulations';

    public const PROMOTIONS = 'promotions';

    // Indexes
    public const INDEX_UUID = 'idx_seance_uuid';

    public const INDEX_FILM = 'idx_seance_film';

    public const INDEX_SALLE = 'idx_seance_salle';

    public const INDEX_FILM_DATE = 'idx_seance_film_date';

    public const INDEX_SALLE_DATE = 'idx_seance_salle_date';

    public const INDEX_DATE = 'idx_seance_date';

    public const INDEX_HEURE = 'idx_seance_heure';

    public const INDEX_3D = 'idx_seance_3d';

    public const INDEX_DATE_STATUT = 'idx_seance_date_statut';

    public const INDEX_PLACEMENT = 'idx_seance_placement';

    public const INDEX_TARIFICATION = 'idx_seance_tarification'; // GIN index sur JSONB

    public const INDEX_VERSION = 'idx_seance_version';

    public const INDEX_STATUT = 'idx_seance_statut';

    public const INDEX_OPTIONS = 'idx_seance_options'; // GIN index sur JSONB

    public const INDEX_PROGRAMMATION = 'idx_seance_programmation';

    public const INDEX_CONFIG_TECHNIQUE = 'idx_seance_config_technique';

    public const INDEX_METADONNEES_COMMERCIALES = 'idx_seance_metadonnees_commerciales';

    public const INDEX_QUALITE_PROJECTION = 'idx_seance_qualite_projection';

    public const INDEX_QUALITE_SONORE = 'idx_seance_qualite_sonore';

    public const INDEX_DUREE_ADDITIONNELLE = 'idx_seance_duree_additionnelle';

    // Foreign Keys
    public const FK_FILM_ID = 'fk_seance_film';

    public const FK_SALLE_ID = 'fk_seance_salle';

    // Constraints
    public const CONSTRAINT_HORAIRES_CHECK = 'chk_seance_horaires';

    public const CONSTRAINT_STATUT_CHECK = 'chk_seance_statut';

    public const CONSTRAINT_VERSION_CHECK = 'chk_seance_version';

    public const CONSTRAINT_TAUX_TVA_CHECK = 'chk_seance_taux_tva';

    public const UNIQUE_SALLE_HEURE = 'uniq_seance_salle_heure';

    public const UNIQUE_SALLE_HORAIRE = 'uniq_seance_salle_horaire';

    public const CHECK_HORAIRES_COHERENTS = 'chk_seance_horaires_coherents';

    public const CHECK_PRIX_POSITIF = 'chk_seance_prix_positif';

    public const CHECK_PLACES_COHERENTES = 'chk_seance_places_coherentes';

    public const CHECK_TVA_VALIDE = 'chk_seance_tva_valide';

    public const CHECK_VENTES_COHERENTES = 'chk_seance_ventes_coherentes';

    public const CONSTRAINT_NO_OVERLAP = 'excl_seance_no_overlap';
}
