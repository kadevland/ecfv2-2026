<?php

declare(strict_types=1);

namespace App\Infrastructure\Database\Schemas\Cinema;

/**
 * Schema pour la collection cinemas_public
 * Centralise les noms de champs et structure
 */
final class CinemaPublicSchema
{
    public const CONNECTION = 'mongodb';

    public const COLLECTION = 'cinemas_public';

    // Champs principaux
    public const CINEMA_ID = 'cinema_id';

    public const NOM = 'nom';

    public const DESCRIPTION = 'description';

    public const ADRESSE = 'adresse';

    public const VILLE = 'ville';

    public const CODE_POSTAL = 'code_postal';

    public const PAYS = 'pays';

    public const TELEPHONE = 'telephone';

    public const EMAIL = 'email';

    public const HORAIRES_OUVERTURE = 'horaires_ouverture';

    public const SERVICES = 'services';

    public const LATITUDE = 'latitude';

    public const LONGITUDE = 'longitude';

    public const NOMBRE_SALLES = 'nombre_salles';

    public const SALLES = 'salles';

    public const TOTAL_PLACES = 'total_places';

    public const STATUT = 'statut';

    // Champs des salles
    public const SALLES_SALLE_ID = 'salles.salle_id';

    public const SALLES_NOM = 'salles.nom';

    public const SALLES_CAPACITE = 'salles.capacite';

    public const SALLES_TYPE_ECRAN = 'salles.type_ecran';

    public const SALLES_TECHNOLOGIES = 'salles.technologies';

    // Timestamps
    public const CREATED_AT = 'created_at';

    public const UPDATED_AT = 'updated_at';

    public const DELETED_AT = 'deleted_at';

    /**
     * Structure d'une salle dans le tableau
     */
    public static function salleStructure(array $data): array
    {
        return [
            'salle_id'          => $data['salle_id'],
            'nom'               => $data['nom'],
            'capacite'          => (int) $data['capacite'],
            'capacite_pmr'      => (int) ($data['capacite_pmr'] ?? 0),
            'type_ecran'        => $data['type_ecran'],
            'technologies'      => $data['technologies'] ?? [],
            'statut'            => $data['statut'] ?? 'active',
            'climatisation'     => (bool) ($data['climatisation'] ?? false),
            'accessibilite_pmr' => (bool) ($data['accessibilite_pmr'] ?? false),
        ];
    }

    /**
     * Valeurs de statut valides
     */
    public static function getStatutsValides(): array
    {
        return \App\Domain\Cinema\Enums\StatusCinemaEnum::values();
    }

    /**
     * Services disponibles
     */
    public static function getServicesDisponibles(): array
    {
        return [
            'parking',
            'restaurant',
            'bar',
            'accessibilite_pmr',
            'climatisation',
            'wifi',
            'dolby_atmos',
            'imax',
            '3d',
            '4dx',
        ];
    }

    /**
     * Structure des horaires d'ouverture
     */
    public static function horairesStructure(array $horaires = []): array
    {
        $default = [
            'lundi'    => null,
            'mardi'    => null,
            'mercredi' => null,
            'jeudi'    => null,
            'vendredi' => null,
            'samedi'   => null,
            'dimanche' => null,
        ];

        return array_merge($default, $horaires);
    }

    /**
     * Structure complète du document
     */
    public static function documentStructure(array $data): array
    {
        return [
            self::CINEMA_ID          => $data['cinema_id'],
            self::NOM                => $data['nom'],
            self::DESCRIPTION        => $data['description'] ?? null,
            self::ADRESSE            => $data['adresse'],
            self::VILLE              => $data['ville'],
            self::CODE_POSTAL        => $data['code_postal'],
            self::PAYS               => $data['pays'] ?? 'FR',
            self::TELEPHONE          => $data['telephone'] ?? null,
            self::EMAIL              => $data['email'] ?? null,
            self::HORAIRES_OUVERTURE => $data['horaires_ouverture'] ?? self::horairesStructure(),
            self::SERVICES           => $data['services'] ?? [],
            self::NOMBRE_SALLES      => (int) ($data['nombre_salles'] ?? 0),
            self::SALLES             => $data['salles'] ?? [],
            self::STATUT             => $data['statut'],
        ];
    }
}
