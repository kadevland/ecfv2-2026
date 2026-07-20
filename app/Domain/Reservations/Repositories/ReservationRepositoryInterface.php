<?php

declare(strict_types=1);

namespace App\Domain\Reservations\Repositories;

use App\Domain\User\ValueObjects\UserId;
use App\Domain\Cinema\ValueObjects\SeanceId;
use App\Domain\Reservations\Entities\Reservation;
use App\Domain\Shared\ValueObjects\PaginationCriteria;
use App\Domain\Reservations\ValueObjects\ReservationId;
use App\Domain\Shared\ValueObjects\PaginatedCollection;

interface ReservationRepositoryInterface
{
    public function save(Reservation $reservation): bool;

    public function findById(ReservationId $id): ?Reservation;

    /**
     * @param array<string, mixed> $filters
     */
    public function findByUserId(UserId $userId, array $filters = [], int $page = 1, int $perPage = 10): PaginatedCollection;

    /**
     * @return array<Reservation>
     */
    public function findBySeanceId(SeanceId $seanceId): array;

    public function findByNumero(string $numeroReservation): ?Reservation;

    /**
     * @return array<Reservation>
     */
    public function findByStatut(string $statut): array;

    /**
     * @return array<Reservation>
     */
    public function findExpired(): array;

    /**
     * @return array<Reservation>
     */
    public function findPending(): array;

    /**
     * @return array<Reservation>
     */
    public function findConfirmed(): array;

    public function findByQrCode(string $qrCode): ?Reservation;

    public function delete(ReservationId $id): bool;

    public function exists(ReservationId $id): bool;

    public function findWithPagination(PaginationCriteria $criteria): PaginatedCollection;

    public function nextIdentity(): ReservationId;

    public function generateReservationNumber(): string;

    public function countBySeance(SeanceId $seanceId): int;

    public function getReservedSeatsForSeance(SeanceId $seanceId): int;

    /**
     * Find multiple reservations by their IDs
     *
     * @param array<string> $ids Array of UUID strings
     * @return array<string, Reservation> Keyed by UUID
     */
    public function findByIds(array $ids): array;
}
