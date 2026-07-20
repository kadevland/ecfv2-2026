<?php

declare(strict_types=1);

namespace App\Infrastructure\Database\Schemas\Cinema;

/**
 * Schema pour la collection seances_public MongoDB
 * Collection read-side pour l'affichage public des séances
 */
final class SeancePublicSchema
{
    public const CONNECTION = 'mongodb';

    public const COLLECTION = 'seance_publics';

    // Identifiants
    public const SEANCE_ID = 'seance_id';

    public const FILM_ID = 'film_id';

    public const SALLE_ID = 'salle_id';

    public const CINEMA_ID = 'cinema_id';

    // Informations dénormalisées
    public const FILM_TITRE = 'film_titre';

    public const SALLE_NOM = 'salle_nom';

    public const CINEMA_NOM = 'cinema_nom';

    // Dates et horaires
    public const DATE_HEURE_DEBUT = 'date_heure_debut';

    public const DATE_HEURE_FIN = 'date_heure_fin';

    // Version et technologies
    public const VERSION = 'version';

    public const TECHNOLOGIES = 'technologies';

    // Tarification
    public const TARIFICATION = 'tarification';

    // Places
    public const PLACES_TOTALES = 'places_totales';

    public const PLACES_DISPONIBLES = 'places_disponibles';

    public const EST_COMPLETE = 'est_complete';

    public const PLACEMENT_LIBRE = 'placement_libre';

    // Statut
    public const STATUT = 'statut';

    // Timestamps
    public const CREATED_AT = 'created_at';

    public const UPDATED_AT = 'updated_at';

    public const DELETED_AT = 'deleted_at';

    /**
     * Structure complète du document séance publique
     */
    public static function documentStructure(array $data): array
    {
        return [
            self::SEANCE_ID          => $data['seance_id'],
            self::FILM_ID            => $data['film_id'],
            self::SALLE_ID           => $data['salle_id'],
            self::CINEMA_ID          => $data['cinema_id'],
            self::FILM_TITRE         => $data['film_titre'],
            self::SALLE_NOM          => $data['salle_nom'],
            self::CINEMA_NOM         => $data['cinema_nom'],
            self::DATE_HEURE_DEBUT   => $data['date_heure_debut'],
            self::DATE_HEURE_FIN     => $data['date_heure_fin'],
            self::VERSION            => $data['version'] ?? 'VF',
            self::TECHNOLOGIES       => $data['technologies'] ?? [],
            self::TARIFICATION       => $data['tarification'] ?? [],
            self::STATUT             => $data['statut'] ?? 'PROGRAMMEE',
            self::PLACES_TOTALES     => (int) ($data['places_totales'] ?? 0),
            self::PLACES_DISPONIBLES => (int) ($data['places_disponibles'] ?? 0),
            self::EST_COMPLETE       => (bool) ($data['est_complete'] ?? false),
            self::PLACEMENT_LIBRE    => (bool) ($data['placement_libre'] ?? false),
        ];
    }

    /**
     * Versions disponibles pour les séances
     */
    public static function getVersionsDisponibles(): array
    {
        return \App\Domain\Cinema\Enums\VersionFilmEnum::values();
    }

    /**
     * Statuts possibles pour une séance
     */
    public static function getStatutsPossibles(): array
    {
        return array_column(\App\Domain\Enums\StatutSeance::cases(), 'value');
    }

    /**
     * Technologies disponibles (qualité projection + sonore)
     */
    public static function getTechnologiesDisponibles(): array
    {
        $projections = \App\Domain\Cinema\Enums\QualiteProjection::getValues();
        $sonores     = \App\Domain\Cinema\Enums\QualiteSonore::getValues();

        return array_merge($projections, $sonores);
    }
}
