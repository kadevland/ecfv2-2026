<?php

declare(strict_types=1);

namespace App\Infrastructure\Database\Schemas\Reservations;

use App\Domain\Enums\StatutReservation;

/**
 * Schema pour la collection reservations_public
 * Centralise les noms de champs et structure
 */
final class ReservationPublicSchema
{
    public const CONNECTION = 'mongodb';

    public const COLLECTION = 'reservations_public';

    // Champs principaux
    public const RESERVATION_ID = 'reservation_id';

    public const USER_ID = 'user_id';

    public const SEANCE_ID = 'seance_id';

    public const CINEMA_ID = 'cinema_id';

    public const SALLE_ID = 'salle_id';

    public const FILM_ID = 'film_id';

    public const STATUT = 'statut';

    public const NOMBRE_PLACES = 'nombre_places';

    public const PRIX_TOTAL = 'prix_total';

    public const PLACES_RESERVEES = 'places_reservees';

    public const DATE_SEANCE = 'date_seance';

    public const HEURE_SEANCE = 'heure_seance';

    // Informations dénormalisées
    public const FILM_TITRE = 'film_titre';

    public const CINEMA_NOM = 'cinema_nom';

    public const SALLE_NOM = 'salle_nom';

    public const USER_EMAIL = 'user_email';

    // Timestamps
    public const CREATED_AT = 'created_at';

    public const UPDATED_AT = 'updated_at';

    public const DELETED_AT = 'deleted_at';

    /**
     * Statuts valides pour les réservations (utilise l'enum du domaine)
     */
    public static function getStatutsValides(): array
    {
        return array_map(fn (StatutReservation $statut) => $statut->value, StatutReservation::cases());
    }

    /**
     * Structure d'une place réservée
     */
    public static function placeStructure(array $data): array
    {
        return [
            'rangee' => $data['rangee'],
            'numero' => $data['numero'],
            'type'   => $data['type'] ?? 'standard',
            'prix'   => (float) $data['prix'],
        ];
    }

    /**
     * Structure complète du document
     */
    public static function documentStructure(array $data): array
    {
        return [
            self::RESERVATION_ID   => $data['reservation_id'],
            self::USER_ID          => $data['user_id'],
            self::SEANCE_ID        => $data['seance_id'],
            self::CINEMA_ID        => $data['cinema_id'],
            self::SALLE_ID         => $data['salle_id'],
            self::FILM_ID          => $data['film_id'],
            self::STATUT           => $data['statut'],
            self::NOMBRE_PLACES    => (int) $data['nombre_places'],
            self::PRIX_TOTAL       => (float) $data['prix_total'],
            self::PLACES_RESERVEES => $data['places_reservees'] ?? [],
            self::DATE_SEANCE      => $data['date_seance'],
            self::HEURE_SEANCE     => $data['heure_seance'],
            self::FILM_TITRE       => $data['film_titre'] ?? null,
            self::CINEMA_NOM       => $data['cinema_nom'] ?? null,
            self::SALLE_NOM        => $data['salle_nom'] ?? null,
            self::USER_EMAIL       => $data['user_email'] ?? null,
        ];
    }
}
