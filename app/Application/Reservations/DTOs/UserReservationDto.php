<?php

declare(strict_types=1);

namespace App\Application\Reservations\DTOs;

use DateTime;
use DateTimeInterface;

/**
 * DTO pour affichage des réservations côté utilisateur (Mon Compte)
 */
final readonly class UserReservationDto
{
    public function __construct(
        public string $id,
        public string $numeroReservation,
        public string $filmTitre,
        public DateTimeInterface $dateHeureDebut,
        public string $cinemaName,
        public string $salleNom,
        public int $nombrePlaces,
        public int $montantTotal, // En centimes
        public string $statut,
        public ?DateTimeInterface $dateExpiration,
        public ?DateTimeInterface $dateReservation,
        public ?string $commentaires,
        public string $userName,
        public string $userEmail,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'],
            numeroReservation: $data['numeroReservation'],
            filmTitre: $data['filmTitre'],
            dateHeureDebut: new DateTime($data['dateHeureDebut']),
            cinemaName: $data['cinemaName'],
            salleNom: $data['salleNom'],
            nombrePlaces: $data['nombrePlaces'],
            montantTotal: $data['montantTotal'],
            statut: $data['statut'],
            dateExpiration: $data['dateExpiration'] ? new DateTime($data['dateExpiration']) : null,
            dateReservation: $data['dateReservation'] ? new DateTime($data['dateReservation']) : null,
            commentaires: $data['commentaires'] ?? null,
            userName: $data['userName'],
            userEmail: $data['userEmail'],
        );
    }

    /**
     * Vérifie si la réservation est active (non expirée, non annulée)
     */
    public function isActive(): bool
    {
        if ($this->statut === 'annulee') {
            return false;
        }

        if ($this->dateExpiration && $this->dateExpiration < new DateTime) {
            return false;
        }

        return true;
    }

    /**
     * Vérifie si la séance est passée
     */
    public function isPastSeance(): bool
    {
        return $this->dateHeureDebut < new DateTime;
    }

    /**
     * Retourne le badge CSS pour le statut
     */
    public function getStatusBadgeClass(): string
    {
        return match ($this->statut) {
            'payee'     => 'bg-green-100 text-green-800',
            'confirmee' => 'bg-yellow-100 text-yellow-800',
            'en_attente', 'en_attente_paiement' => 'bg-gray-100 text-gray-800',
            'annulee' => 'bg-red-100 text-red-800',
            'expiree' => 'bg-gray-100 text-gray-800',
            default   => 'bg-gray-100 text-gray-800',
        };
    }

    /**
     * Formate le montant en euros
     */
    public function getFormattedAmount(): string
    {
        return number_format($this->montantTotal / 100, 2, ',', ' ') . ' €';
    }

    /**
     * Retourne le label lisible du statut
     */
    public function getStatusLabel(): string
    {
        return match ($this->statut) {
            'payee'     => 'Payée',
            'confirmee' => 'Confirmée',
            'en_attente', 'en_attente_paiement' => 'En attente de paiement',
            'annulee' => 'Annulée',
            'expiree' => 'Expirée',
            default   => ucfirst($this->statut),
        };
    }
}
