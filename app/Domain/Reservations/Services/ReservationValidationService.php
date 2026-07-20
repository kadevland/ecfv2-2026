<?php

declare(strict_types=1);

namespace App\Domain\Reservations\Services;

use DateTime;
use DateTimeInterface;
use App\Domain\Enums\StatutSeance;
use App\Domain\Cinema\Entities\Seance;
use App\Domain\Cinema\ValueObjects\SeanceId;
use App\Domain\Cinema\Repositories\SeanceRepositoryInterface;
use App\Domain\Reservations\Exceptions\ReservationValidationException;
use App\Domain\Reservations\Repositories\ReservationRepositoryInterface;

/**
 * Service de validation métier pour les réservations
 * Centralise toutes les règles de validation business
 */
final class ReservationValidationService
{
    private const MAX_PLACES_PAR_RESERVATION = 10;

    private const MIN_PLACES_PAR_RESERVATION = 1;

    private const DELAI_RESERVATION_HEURES = 1; // Minimum 1h avant la séance

    public function __construct(
        private readonly ReservationRepositoryInterface $reservationRepository,
        private readonly SeanceRepositoryInterface $seanceRepository,
    ) {}

    /**
     * Valide qu'une réservation peut être créée
     */
    public function validateReservationCreation(
        SeanceId $seanceId,
        int $nombrePlaces,
        ?DateTimeInterface $dateReservation = null
    ): void {
        $dateReservation = $dateReservation ?? new DateTime;

        // 1. Vérifier que la séance existe
        $seance = $this->seanceRepository->findById($seanceId);
        if (!$seance) {
            throw new ReservationValidationException('Séance introuvable');
        }

        // 2. Vérifier le nombre de places
        $this->validateNombrePlaces($nombrePlaces);

        // 3. Vérifier les disponibilités
        $this->validatePlacesDisponibles($seanceId, $nombrePlaces);

        // 4. Vérifier les horaires
        $this->validateHorairesReservation($seance, $dateReservation);

        // 5. Vérifier le statut de la séance
        $this->validateStatutSeance($seance);
    }

    /**
     * Retourne les statistiques de disponibilité pour une séance
     */
    /**
     * @return array<string, mixed>
     */
    public function getDisponibiliteSeance(SeanceId $seanceId): array
    {
        $seance = $this->seanceRepository->findById($seanceId);
        if (!$seance) {
            return ['error' => 'Séance introuvable'];
        }

        $capaciteMax       = 100;
        $placesReservees   = $this->reservationRepository->getReservedSeatsForSeance($seanceId);
        $placesDisponibles = $capaciteMax - $placesReservees;
        $tauxOccupation    = round(($placesReservees / $capaciteMax) * 100, 1);

        return [
            'capacite_max'       => $capaciteMax,
            'places_reservees'   => $placesReservees,
            'places_disponibles' => $placesDisponibles,
            'taux_occupation'    => $tauxOccupation,
            'peut_reserver'      => $placesDisponibles > 0,
        ];
    }

    /**
     * Valide le nombre de places
     */
    private function validateNombrePlaces(int $nombrePlaces): void
    {
        if ($nombrePlaces < self::MIN_PLACES_PAR_RESERVATION) {
            throw new ReservationValidationException(
                'Vous devez réserver au moins ' . self::MIN_PLACES_PAR_RESERVATION . ' place'
            );
        }

        if ($nombrePlaces > self::MAX_PLACES_PAR_RESERVATION) {
            throw new ReservationValidationException(
                'Maximum ' . self::MAX_PLACES_PAR_RESERVATION . ' places par réservation'
            );
        }
    }

    /**
     * Valide les places disponibles
     */
    private function validatePlacesDisponibles(SeanceId $seanceId, int $nombrePlaces): void
    {
        // Compter les places déjà réservées (non annulées)
        $placesReservees = $this->reservationRepository->getReservedSeatsForSeance($seanceId);

        // Récupérer la capacité de la salle
        $seance      = $this->seanceRepository->findById($seanceId);
        $capaciteMax = 100;

        $placesDisponibles = $capaciteMax - $placesReservees;

        if ($placesDisponibles < $nombrePlaces) {
            throw new ReservationValidationException(
                "Seulement {$placesDisponibles} places disponibles (capacité: {$capaciteMax}, réservées: {$placesReservees})"
            );
        }
    }

    /**
     * Valide les horaires de réservation
     */
    private function validateHorairesReservation(Seance $seance, DateTimeInterface $dateReservation): void
    {
        $dateSeance = $seance->dateHeureDebut;

        // Vérifier que la séance n'est pas déjà passée
        if ($dateSeance < $dateReservation) {
            throw new ReservationValidationException('Impossible de réserver pour une séance passée');
        }

        // Vérifier le délai minimum avant la séance
        $delaiMinimum = new DateTime($dateSeance->format('Y-m-d H:i:s'));
        $delaiMinimum->modify('-' . self::DELAI_RESERVATION_HEURES . ' hours');

        if ($dateReservation >= $delaiMinimum) {
            throw new ReservationValidationException(
                'Réservations fermées : minimum ' . self::DELAI_RESERVATION_HEURES . ' heure(s) avant la séance'
            );
        }
    }

    /**
     * Valide le statut de la séance
     */
    private function validateStatutSeance(Seance $seance): void
    {
        // Vérifier que la séance n'est pas annulée
        if ($seance->statut === StatutSeance::ANNULEE) {
            throw new ReservationValidationException('Cette séance a été annulée');
        }

        // Vérifier que la séance n'est pas déjà passée
        if ($seance->isPast()) {
            throw new ReservationValidationException('Les réservations sont fermées pour cette séance');
        }
    }
}
