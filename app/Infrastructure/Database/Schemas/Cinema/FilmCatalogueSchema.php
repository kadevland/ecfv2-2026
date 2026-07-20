<?php

declare(strict_types=1);

namespace App\Infrastructure\Database\Schemas\Cinema;

/**
 * Schema pour la collection films_catalogue
 * Centralise les noms de champs et structure
 */
final class FilmCatalogueSchema
{
    public const CONNECTION = 'mongodb';

    public const COLLECTION = 'film_catalogues';

    // Champs principaux
    public const FILM_ID = 'film_id';

    public const TITRE = 'titre';

    public const DESCRIPTION = 'description';

    public const GENRE = 'genre';

    public const DUREE = 'duree';

    public const CLASSIFICATION = 'classification';

    public const DATE_SORTIE = 'date_sortie';

    public const REALISATEUR = 'realisateur';

    public const ACTEURS_PRINCIPAUX = 'acteurs_principaux';

    public const AFFICHE_URL = 'affiche_url';

    public const BANDE_ANNONCE_URL = 'bande_annonce_url';

    public const NOTE_MOYENNE = 'note_moyenne';

    public const NOMBRE_AVIS = 'nombre_avis';

    public const STATUT_DIFFUSION = 'statut_diffusion';

    public const STATUT = 'statut';

    public const CINEMAS_DIFFUSION = 'cinemas_diffusion';

    public const PROCHAINES_SEANCES = 'prochaines_seances';

    // Champs des cinémas de diffusion
    public const CINEMAS_CINEMA_ID = 'cinemas_diffusion.cinema_id';

    public const CINEMAS_NOM_CINEMA = 'cinemas_diffusion.nom_cinema';

    // Timestamps
    public const CREATED_AT = 'created_at';

    public const UPDATED_AT = 'updated_at';

    public const DELETED_AT = 'deleted_at';

    /**
     * Genres de films disponibles
     */
    public static function getGenresDisponibles(): array
    {
        return \App\Domain\Enums\GenreFilm::values();
    }

    /**
     * Classifications d'âge disponibles
     */
    public static function getClassificationsValides(): array
    {
        return \App\Domain\Cinema\Enums\ClassificationFilmEnum::values();
    }

    /**
     * Statuts de diffusion valides
     */
    public static function getStatutsDiffusion(): array
    {
        return \App\Domain\Cinema\Enums\StatusFilmEnum::values();
    }

    /**
     * Structure d'un cinéma de diffusion
     */
    public static function cinemaDiffusionStructure(array $data): array
    {
        return [
            'cinema_id'                => $data['cinema_id'],
            'nom_cinema'               => $data['nom_cinema'],
            'ville'                    => $data['ville'] ?? null,
            'prochaines_seances_count' => (int) ($data['prochaines_seances_count'] ?? 0),
        ];
    }

    /**
     * Structure d'une prochaine séance
     */
    public static function prochaineSeanceStructure(array $data): array
    {
        return [
            'seance_id'          => $data['seance_id'],
            'cinema_id'          => $data['cinema_id'],
            'nom_cinema'         => $data['nom_cinema'],
            'date_heure_debut'   => $data['date_heure_debut'],
            'places_disponibles' => (int) $data['places_disponibles'],
        ];
    }

    /**
     * Structure complète du document
     */
    public static function documentStructure(array $data): array
    {
        return [
            self::FILM_ID            => $data['film_id'],
            self::TITRE              => $data['titre'],
            self::DESCRIPTION        => $data['description'] ?? null,
            self::GENRE              => $data['genre'],
            self::DUREE              => (int) $data['duree'],
            self::CLASSIFICATION     => $data['classification'],
            self::DATE_SORTIE        => $data['date_sortie'],
            self::REALISATEUR        => $data['realisateur'] ?? null,
            self::ACTEURS_PRINCIPAUX => $data['acteurs_principaux'] ?? [],
            self::AFFICHE_URL        => $data['affiche_url'] ?? null,
            self::BANDE_ANNONCE_URL  => $data['bande_annonce_url'] ?? null,
            self::NOTE_MOYENNE       => (float) ($data['note_moyenne'] ?? 0.0),
            self::NOMBRE_AVIS        => (int) ($data['nombre_avis'] ?? 0),
            self::STATUT_DIFFUSION   => $data['statut_diffusion'],
            self::CINEMAS_DIFFUSION  => $data['cinemas_diffusion'] ?? [],
            self::PROCHAINES_SEANCES => $data['prochaines_seances'] ?? [],
        ];
    }
}
