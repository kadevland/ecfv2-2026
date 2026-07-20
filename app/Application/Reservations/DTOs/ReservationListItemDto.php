<?php

declare(strict_types=1);

namespace App\Application\Reservations\DTOs;

use Money\Money;
use DateTimeInterface;

/**
 * DTO pour affichage des réservations dans les listes admin
 * Contient les informations de réservation + utilisateur enrichies
 */
final readonly class ReservationListItemDto
{
    /**
     * @param array<string, mixed> $placesDetails
     */
    public function __construct(
        public string $id,
        public string $numeroReservation,
        public string $userId,
        public string $userEmail,
        public string $userNom,
        public string $userPrenom,
        public string $seanceId,
        public string $filmTitre,
        public DateTimeInterface $seanceDate,
        public string $cinemaName,
        public string $salleName,
        public int $nombrePlaces,
        /** @var array<string, mixed> */ public array $placesDetails,
        public Money $montantTotal,
        public Money $montantHt,
        public string $statut,
        public string $statutLabel,
        public ?DateTimeInterface $dateExpiration,
        public ?string $commentaires,
        public DateTimeInterface $createdAt,
        public DateTimeInterface $updatedAt,
    ) {}

    /**
     * @return mixed
     */
    public function __get(string $name)
    {
        // Compatibilité avec l'accès via propriété dans les vues Blade
        $array = $this->toArray();

        return $array[$name] ?? null;
    }

    /**
     * Convertit en array pour les vues
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'id'                => $this->id,
            'numeroReservation' => $this->numeroReservation,
            'userId'            => $this->userId,
            'emailClient'       => $this->userEmail, // Pour compatibilité avec la vue existante
            'userEmail'         => $this->userEmail,
            'userNom'           => $this->userNom,
            'userPrenom'        => $this->userPrenom,
            'seanceId'          => $this->seanceId,
            'filmTitre'         => $this->filmTitre,
            'seanceDate'        => $this->seanceDate,
            'cinemaName'        => $this->cinemaName,
            'salleName'         => $this->salleName,
            'nombrePlaces'      => $this->nombrePlaces,
            'placesDetails'     => $this->placesDetails,
            'montantTotal'      => $this->montantTotal,
            'montantHt'         => $this->montantHt,
            'statut'            => $this->statut,
            'statutLabel'       => $this->statutLabel,
            'dateExpiration'    => $this->dateExpiration,
            'commentaires'      => $this->commentaires,
            'createdAt'         => $this->createdAt,
            'updatedAt'         => $this->updatedAt,
        ];
    }
}
