<?php

declare(strict_types=1);

namespace App\Domain\Cinema\Repositories;

use App\Domain\Cinema\Entities\Salle;
use App\Domain\Cinema\ValueObjects\SalleId;
use App\Domain\Cinema\ValueObjects\CinemaId;
use App\Domain\Shared\ValueObjects\PaginationCriteria;
use App\Domain\Shared\ValueObjects\PaginatedCollection;

interface SalleRepositoryInterface
{
    public function save(Salle $salle): bool;

    public function findById(SalleId $id): ?Salle;

    /**
     * Find salles by multiple IDs (batch loading)
     *
     * @param array<SalleId> $ids
     * @return array<string, Salle> Indexed by SalleId value for O(1) access
     */
    public function findByIds(array $ids): array;

    /**
     * @return array<Salle>
     */
    public function findByCinemaId(CinemaId $cinemaId): array;

    /**
     * @return array<Salle>
     */
    public function findAllActive(): array;

    // public function findByNumero(int $numero, CinemaId $cinemaId): ?Salle;

    // public function findWithEquipment(string $equipment): array;

    // public function findAccessible(): array;

    /**
     * @return array<Salle>
     */
    public function findByCapacityRange(int $minCapacity, int $maxCapacity): array;

    public function delete(SalleId $id): bool;

    public function exists(SalleId $id): bool;

    public function findWithPagination(PaginationCriteria $criteria): PaginatedCollection;

    /**
     * Find paginated salles with cinema names for listing (optimized)
     *
     * @return array<string, mixed>
     */
    public function findWithPaginationAndCinemaNames(PaginationCriteria $criteria): array;

    public function countByCinema(CinemaId $cinemaId): int;

    public function nextIdentity(): SalleId;

    public function getTotalCapacityByCinema(CinemaId $cinemaId): int;

    public function findLargestByCinema(CinemaId $cinemaId): ?Salle;

    /**
     * Check if cinema has at least one accessible (PMR) active room
     */
    public function hasAccessibleRoomByCinema(CinemaId $cinemaId): bool;

    /**
     * Get formatted salles data for select dropdowns
     *
     * @return array<string, string>
     */
    public function findAllForSelect(): array;
}
