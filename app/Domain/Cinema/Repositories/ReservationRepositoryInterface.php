<?php

declare(strict_types=1);

namespace App\Domain\Cinema\Repositories;

use DateTimeInterface;
use App\Domain\Cinema\Entities\Reservation;
use App\Domain\Cinema\ValueObjects\SeanceId;
use App\Domain\Cinema\ValueObjects\ReservationId;
use App\Domain\Cinema\ValueObjects\UtilisateurId;
use App\Domain\Shared\ValueObjects\PaginationCriteria;
use App\Domain\Shared\ValueObjects\PaginatedCollection;

interface ReservationRepositoryInterface
{
    public function save(Reservation $reservation): bool;

    public function findById(ReservationId $id): ?Reservation;

    /**
     * @return array<Reservation>
     */
    public function findBySeanceId(SeanceId $seanceId): array;

    /**
     * Find reservations by multiple seance IDs (batch loading)
     *
     * @param array<SeanceId> $seanceIds
     * @return array<string, array<Reservation>> Indexed by SeanceId value, each containing array of reservations
     */
    public function findBySeanceIds(array $seanceIds): array;

    /**
     * @return array<Reservation>
     */
    public function findByUtilisateurId(UtilisateurId $utilisateurId): array;

    /**
     * @return array<Reservation>
     */
    public function findByDate(DateTimeInterface $date): array;

    /**
     * @return array<Reservation>
     */
    public function findByDateRange(DateTimeInterface $startDate, DateTimeInterface $endDate): array;

    /**
     * @return array<Reservation>
     */
    public function findByStatut(string $statut): array;

    public function findBySeanceAndUtilisateur(SeanceId $seanceId, UtilisateurId $utilisateurId): ?Reservation;

    public function countBySeanceId(SeanceId $seanceId): int;

    public function countPlacesBySeanceId(SeanceId $seanceId): int;

    public function delete(ReservationId $id): bool;

    public function exists(ReservationId $id): bool;

    public function findWithPagination(PaginationCriteria $criteria): PaginatedCollection;

    public function nextIdentity(): ReservationId;

    /**
     * @return array<Reservation>
     */
    public function findExpiredReservations(): array;

    /**
     * @return array<Reservation>
     */
    public function findPendingReservations(): array;
}
