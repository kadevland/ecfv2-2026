<?php

declare(strict_types=1);

namespace App\Infrastructure\Database\Schemas\Cinema;

/**
 * Schema pour la collection seances_live
 * Centralise les noms de champs et structure
 */
final class SeanceLiveSchema
{
    public const CONNECTION = 'mongodb';

    public const COLLECTION = 'seances_live';

    // Champs principaux
    public const SEANCE_ID = 'seance_id';

    public const FILM_ID = 'film_id';

    public const CINEMA_ID = 'cinema_id';

    public const SALLE_ID = 'salle_id';

    public const TITRE_FILM = 'titre_film';

    public const NOM_CINEMA = 'nom_cinema';

    public const NOM_SALLE = 'nom_salle';

    public const DATE_HEURE_DEBUT = 'date_heure_debut';

    public const DATE_HEURE_FIN = 'date_heure_fin';

    public const PLACES_TOTALES = 'places_totales';

    public const PLACES_VENDUES = 'places_vendues';

    public const PLACES_RESERVEES = 'places_reservees';

    public const PLACES_DISPONIBLES = 'places_disponibles';

    public const TARIFS = 'tarifs';

    public const STATUT = 'statut';

    public const VERSION = 'version';

    public const SOUS_TITRES = 'sous_titres';

    public const QUALITE_PROJECTION = 'qualite_projection';

    // Timestamps
    public const CREATED_AT = 'created_at';

    public const UPDATED_AT = 'updated_at';

    public const DELETED_AT = 'deleted_at';

    /**
     * Valeurs de statut valides
     */
    public static function getStatutsValides(): array
    {
        return \App\Domain\Cinema\Enums\StatusSeanceEnum::values();
    }

    /**
     * Versions de film disponibles
     */
    public static function getVersionsValides(): array
    {
        return \App\Domain\Cinema\Enums\VersionFilmEnum::values();
    }

    /**
     * Qualités de projection disponibles
     */
    public static function getQualitesProjection(): array
    {
        return \App\Domain\Cinema\Enums\ProjectionQualityEnum::values();
    }

    /**
     * Structure des tarifs
     */
    public static function tarifsStructure(array $tarifs = []): array
    {
        $default = [
            'plein'    => 0.0,
            'reduit'   => 0.0,
            'enfant'   => 0.0,
            'senior'   => 0.0,
            'etudiant' => 0.0,
        ];

        return array_merge($default, $tarifs);
    }

    /**
     * Structure pour les prochaines séances (dans FilmCatalogue)
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
            self::SEANCE_ID          => $data['seance_id'],
            self::FILM_ID            => $data['film_id'],
            self::CINEMA_ID          => $data['cinema_id'],
            self::SALLE_ID           => $data['salle_id'],
            self::TITRE_FILM         => $data['titre_film'],
            self::NOM_CINEMA         => $data['nom_cinema'],
            self::NOM_SALLE          => $data['nom_salle'],
            self::DATE_HEURE_DEBUT   => $data['date_heure_debut'],
            self::DATE_HEURE_FIN     => $data['date_heure_fin'],
            self::PLACES_TOTALES     => (int) $data['places_totales'],
            self::PLACES_VENDUES     => (int) ($data['places_vendues'] ?? 0),
            self::PLACES_RESERVEES   => (int) ($data['places_reservees'] ?? 0),
            self::PLACES_DISPONIBLES => (int) $data['places_disponibles'],
            self::TARIFS             => $data['tarifs'] ?? self::tarifsStructure(),
            self::STATUT             => $data['statut'],
            self::VERSION            => $data['version'],
            self::SOUS_TITRES        => $data['sous_titres'] ?? false,
            self::QUALITE_PROJECTION => $data['qualite_projection'],
        ];
    }
}
