<?php

declare(strict_types=1);

namespace App\Infrastructure\Database\Schemas\Cinema;

/**
 * Schema pour la collection salles_public
 * Centralise les noms de champs et structure
 */
final class SallePublicSchema
{
    public const CONNECTION = 'mongodb';

    public const COLLECTION = 'salles_public';

    // Champs principaux
    public const SALLE_ID = 'salle_id';

    public const CINEMA_ID = 'cinema_id';

    public const NOM = 'nom';

    public const CAPACITE = 'capacite';

    public const TYPE_ECRAN = 'type_ecran';

    public const TECHNOLOGIES = 'technologies';

    public const PLAN_SALLE = 'plan_salle';

    // Informations dénormalisées
    public const CINEMA_NOM = 'cinema_nom';

    public const CINEMA_VILLE = 'cinema_ville';

    public const SEANCES_ACTUELLES = 'seances_actuelles';

    // Timestamps
    public const CREATED_AT = 'created_at';

    public const UPDATED_AT = 'updated_at';

    public const DELETED_AT = 'deleted_at';

    /**
     * Types d'écran valides
     */
    public static function getTypesEcranValides(): array
    {
        return [
            'standard',
            'imax',
            'dolby_cinema',
            '4dx',
            'screenx',
        ];
    }

    /**
     * Technologies disponibles
     */
    public static function getTechnologiesDisponibles(): array
    {
        return [
            '2d',
            '3d',
            'dolby_atmos',
            'thx',
            'laser',
            'hfr',
        ];
    }

    /**
     * Structure d'une séance dans seances_actuelles
     */
    public static function seanceStructure(array $data): array
    {
        return [
            'seance_id'          => $data['seance_id'],
            'film_titre'         => $data['film_titre'],
            'date_heure'         => $data['date_heure'],
            'places_disponibles' => (int) $data['places_disponibles'],
            'tarifs'             => $data['tarifs'] ?? [],
        ];
    }

    /**
     * Structure du plan de salle
     */
    public static function planSalleStructure(array $data): array
    {
        return [
            'rangees'       => $data['rangees'] ?? [],
            'places_pmr'    => $data['places_pmr'] ?? [],
            'zones_premium' => $data['zones_premium'] ?? [],
        ];
    }

    /**
     * Structure complète du document
     */
    public static function documentStructure(array $data): array
    {
        return [
            self::SALLE_ID          => $data['salle_id'],
            self::CINEMA_ID         => $data['cinema_id'],
            self::NOM               => $data['nom'],
            self::CAPACITE          => (int) $data['capacite'],
            self::TYPE_ECRAN        => $data['type_ecran'],
            self::TECHNOLOGIES      => $data['technologies'] ?? [],
            self::PLAN_SALLE        => $data['plan_salle'] ?? null,
            self::CINEMA_NOM        => $data['cinema_nom'] ?? null,
            self::CINEMA_VILLE      => $data['cinema_ville'] ?? null,
            self::SEANCES_ACTUELLES => $data['seances_actuelles'] ?? [],
        ];
    }
}
