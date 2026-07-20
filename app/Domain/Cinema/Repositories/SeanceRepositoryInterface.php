<?php

declare(strict_types=1);

namespace App\Domain\Cinema\Repositories;

use DateTimeInterface;
use App\Domain\Cinema\Entities\Seance;
use App\Domain\Cinema\ValueObjects\FilmId;
use App\Domain\Cinema\ValueObjects\SalleId;
use App\Domain\Cinema\ValueObjects\CinemaId;
use App\Domain\Cinema\ValueObjects\SeanceId;
use App\Domain\Shared\ValueObjects\PaginationCriteria;
use App\Domain\Shared\ValueObjects\PaginatedCollection;

interface SeanceRepositoryInterface
{
    public function save(Seance $seance): bool;

    public function findById(SeanceId $id): ?Seance;

    /**
     * @return array<Seance>
     */
    public function findByFilmId(FilmId $filmId): array;

    /**
     * @return array<Seance>
     */
    public function findBySalleId(SalleId $salleId): array;

    /**
     * @return array<Seance>
     */
    public function findByCinemaId(CinemaId $cinemaId): array;

    /**
     * @return array<Seance>
     */
    public function findByDate(DateTimeInterface $date): array;

    /**
     * @return array<Seance>
     */
    public function findByDateRange(DateTimeInterface $startDate, DateTimeInterface $endDate): array;

    /**
     * @return array<Seance>
     */
    public function findUpcoming(): array;

    /**
     * @return array<Seance>
     */
    public function findToday(): array;

    /**
     * @return array<Seance>
     */
    public function findByFilmAndDate(FilmId $filmId, DateTimeInterface $date): array;

    /**
     * @return array<Seance>
     */
    public function findBySalleAndDate(SalleId $salleId, DateTimeInterface $date): array;

    public function findAvailableSeats(SeanceId $seanceId): int;

    public function delete(SeanceId $id): bool;

    public function exists(SeanceId $id): bool;

    public function findWithPagination(PaginationCriteria $criteria): PaginatedCollection;

    public function nextIdentity(): SeanceId;

    /**
     * @return array<Seance>
     */
    public function findConflictingSeances(
        SalleId $salleId,
        DateTimeInterface $startTime,
        DateTimeInterface $endTime,
        ?SeanceId $excludeSeanceId = null
    ): array;

    /**
     * Find multiple seances by their IDs with relations
     *
     * @param array<string> $ids Array of UUID strings
     * @return array<string, Seance> Keyed by UUID
     */
    public function findByIdsWithRelations(array $ids): array;
}
