<?php

declare(strict_types=1);

namespace App\Infrastructure\Database\Schemas\Cinema;

use App\Infrastructure\Database\Schemas\DatabaseSchemas;

/**
 * Schema constants for films table
 * Catalogue films avec métadonnées complètes
 */
final class FilmSchema
{
    // Table configuration
    public const SCHEMA = DatabaseSchemas::CINEMA;

    public const TABLE = 'films';

    public const FULL_TABLE = self::SCHEMA . '.' . self::TABLE;

    public const PRIMARY_KEY = 'db_id';

    public const CONNECTION = 'pgsql';

    // Columns
    public const ID = 'uuid';

    public const TITRE = 'titre';

    public const TITRE_ORIGINAL = 'titre_original';

    public const SYNOPSIS = 'synopsis';

    public const GENRE = 'genre';

    public const PAYS_ORIGINE = 'pays_origine';

    public const TITRE_FR = 'titre_fr';

    public const REALISATEUR = 'realisateur';

    public const REALISATEURS = 'realisateurs';

    public const PRODUCTEUR = 'producteur';

    public const IMAGES_ADDITIONNELLES = 'images_additionnelles';

    public const NOTE_CRITIQUE = 'note_critique';

    public const STATUT = 'statut';

    public const METADONNEES_TECHNIQUES = 'metadonnees_techniques';

    public const ACTEURS_PRINCIPAUX = 'acteurs_principaux';

    public const GENRES = 'genres';

    public const DUREE_MINUTES = 'duree_minutes';

    public const CLASSIFICATION = 'classification';

    public const LANGUE_ORIGINALE = 'langue_originale';

    public const SOUS_TITRES = 'sous_titres';

    public const RESUME = 'resume';

    public const DATE_SORTIE = 'date_sortie';

    public const DATE_FIN_EXPLOITATION = 'date_fin_exploitation';

    public const NOTE_PRESSE = 'note_presse';

    public const NOTE_PUBLIC = 'note_public';

    public const NOTE_MOYENNE_AVIS = 'note_moyenne_avis';

    public const NOMBRE_AVIS = 'nombre_avis';

    public const AFFICHE_URL = 'affiche_url';

    public const BANDE_ANNONCE_URL = 'bande_annonce_url';

    public const EST_ACTIF = 'est_actif';

    public const CREATED_AT = 'created_at';

    public const UPDATED_AT = 'updated_at';

    // Indexes
    public const INDEX_UUID = 'idx_film_uuid';

    public const INDEX_TITRE = 'idx_film_titre';

    public const INDEX_GENRE = 'idx_film_genre';

    public const INDEX_REALISATEUR = 'idx_film_realisateur';

    public const INDEX_REALISATEURS = 'idx_film_realisateurs'; // GIN index sur JSONB

    public const INDEX_GENRES = 'idx_film_genres'; // GIN index sur JSONB

    public const INDEX_ACTEURS = 'idx_film_acteurs'; // GIN index sur JSONB

    public const INDEX_DATE_SORTIE = 'idx_film_date_sortie';

    public const INDEX_CLASSIFICATION = 'idx_film_classification';

    public const INDEX_STATUT = 'idx_film_statut';

    public const INDEX_EST_ACTIF = 'idx_film_actif';

    public const INDEX_DUREE = 'idx_film_duree';

    public const INDEX_DATE_FIN_EXPLOITATION = 'idx_film_date_fin_exploitation';

    public const INDEX_NOTE_MOYENNE = 'idx_film_note_moyenne_avis';

    public const INDEX_GENRE_STATUT = 'idx_film_genre_statut';

    public const INDEX_DATE_STATUT = 'idx_film_date_statut';

    public const INDEX_IMAGES = 'idx_film_images_additionnelles';

    public const INDEX_METADONNEES = 'idx_film_metadonnees_techniques';

    // Unique constraints
    public const UNIQUE_TITRE_DATE = 'uniq_film_titre_date';

    // Constraints
    public const CONSTRAINT_DUREE_CHECK = 'chk_film_duree';

    public const CONSTRAINT_NOTES_CHECK = 'chk_film_notes';

    public const CONSTRAINT_CLASSIFICATION_CHECK = 'chk_film_classification';
}
