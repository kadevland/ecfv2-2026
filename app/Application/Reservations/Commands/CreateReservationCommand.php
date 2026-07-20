<?php

declare(strict_types=1);

namespace App\Application\Reservations\Commands;

use App\Application\Contracts\CommandInterface;

final readonly class CreateReservationCommand implements CommandInterface
{
    /**
     * @param array<string, mixed>|null $seats Places numérotées sélectionnées
     * @param array<string, int>|null $placesByTarif Places par tarif ['normal' => 2, 'reduit' => 1, 'enfant' => 0]
     * @param array<string, mixed>|null $placesDetails Détails finaux des places
     */
    public function __construct(
        public readonly string $userId,
        public readonly string $seanceId,
        /** @var array<string, mixed>|null */ public readonly ?array $seats = null,        // Places numérotées sélectionnées
        public readonly ?int $nombrePlaces = null,    // Nombre de places (placement libre)
        /** @var array<string, int>|null */ public readonly ?array $placesByTarif = null, // Places par tarif ['normal' => 2, 'reduit' => 1, 'enfant' => 0]
        /** @var array<string, mixed>|null */ public readonly ?array $placesDetails = null, // Détails finaux des places
        public readonly ?int $montantTotal = null,    // en centimes
        public readonly ?int $montantHt = null,       // en centimes
        public readonly ?int $tauxTva = null,         // en basis points
        public readonly ?string $commentaires = null,
        public readonly ?string $dateExpiration = null,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromRequest(array $data): self
    {
        // Déterminer si on a des places numérotées ou un nombre
        $seats         = null;
        $nombrePlaces  = null;
        $placesByTarif = null;

        if (isset($data['seats']) && is_array($data['seats'])) {
            $seats = $data['seats'];
        } elseif (isset($data['nombre_places'])) {
            $nombrePlaces = (int) $data['nombre_places'];
        } elseif (isset($data['places']) && is_array($data['places'])) {
            // Places par tarif
            $placesByTarif = $data['places'];
            $nombrePlaces  = array_sum(array_map('intval', $placesByTarif));
        }

        return new self(
            userId: $data['user_id'],
            seanceId: $data['seance_id'],
            seats: $seats,
            nombrePlaces: $nombrePlaces,
            placesByTarif: $placesByTarif,
            placesDetails: $data['places_details'] ?? null,
            montantTotal: $data['montant_total'] ?? null,
            montantHt: $data['montant_ht'] ?? null,
            tauxTva: $data['taux_tva'] ?? null,
            commentaires: $data['commentaires'] ?? null,
            dateExpiration: $data['date_expiration'] ?? null,
        );
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return self::fromRequest($data);
    }
}
