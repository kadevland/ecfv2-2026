<?php

declare(strict_types=1);

namespace App\Infrastructure\Database\Schemas\Cinema;

use App\Infrastructure\Database\Schemas\DatabaseSchemas;

/**
 * Schema constants for salles table
 * Salles de projection avec configuration sièges
 */
final class SalleSchema
{
    // Table configuration
    public const SCHEMA = DatabaseSchemas::CINEMA;

    public const TABLE = 'salles';

    public const FULL_TABLE = self::SCHEMA . '.' . self::TABLE;

    public const PRIMARY_KEY = 'db_id';

    public const CONNECTION = 'pgsql';

    // Columns
    public const ID = 'uuid';               // UUID métier Salle

    // Foreign Keys vers Cinema
    public const CINEMA_KEY = 'cinema_db_id';   // FK technique (performance)

    public const CINEMA_ID = 'cinema_uuid';     // FK métier (business logic)

    // Champs de base
    public const NOM = 'nom';

    public const CAPACITE_TOTALE = 'capacite_totale';

    public const NOMBRE_RANGEES = 'nombre_rangees';

    public const PLACES_PAR_RANGEE = 'places_par_rangee';

    // Places (seulement standard et PMR)
    public const PLACES_STANDARD = 'places_standard';

    public const PLACES_PMR = 'places_pmr';

    // Qualités en JSON
    public const QUALITE_PROJECTION = 'qualite_projection';

    public const QUALITE_SONORE = 'qualite_sonore';

    // Équipements et configuration
    public const CLIMATISATION = 'climatisation';

    public const ACCESSIBILITE_PMR = 'accessibilite_pmr';

    public const PLAN_SALLE = 'plan_salle';

    // Statut et gestion
    public const STATUT = 'statut';

    // Informations techniques
    // public const SUPERFICIE_M2 = 'superficie_m2';
    // public const HAUTEUR_PLAFOND = 'hauteur_plafond';
    // public const NOTES_TECHNIQUES = 'notes_techniques';

    public const CREATED_AT = 'created_at';

    public const UPDATED_AT = 'updated_at';

    // Indexes
    public const INDEX_UUID = 'idx_salle_uuid';

    public const INDEX_CINEMA_DB = 'idx_salle_cinema_db';

    public const INDEX_CINEMA = 'idx_salle_cinema';

    public const INDEX_NOM = 'idx_salle_nom';

    public const INDEX_CAPACITE = 'idx_salle_capacite';

    public const INDEX_STATUT = 'idx_salle_statut';

    // Index GIN sur JSONB
    public const INDEX_QUALITE_PROJECTION = 'idx_salle_qualite_projection';

    public const INDEX_QUALITE_SONORE = 'idx_salle_qualite_sonore';

    public const INDEX_PLAN_SALLE = 'idx_salle_plan_salle';

    // Index composés
    public const INDEX_CINEMA_STATUT = 'idx_salle_cinema_statut';

    public const INDEX_CAPACITE_STATUT = 'idx_salle_capacite_statut';

    // Unique constraints
    public const UNIQUE_CINEMA_NOM = 'uniq_salle_cinema_nom';

    // Foreign Keys
    public const FK_CINEMA_ID = 'fk_salle_cinema';
}
