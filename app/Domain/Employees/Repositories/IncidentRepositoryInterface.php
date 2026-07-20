<?php

declare(strict_types=1);

namespace App\Domain\Employees\Repositories;

use App\Domain\Enums\TypeIncident;
use App\Domain\Enums\StatutIncident;
use App\Domain\Enums\SeveriteIncident;
use App\Domain\Cinema\ValueObjects\SalleId;
use App\Domain\Employees\Entities\Incident;
use App\Domain\Cinema\ValueObjects\CinemaId;
use App\Domain\Employees\ValueObjects\EmploiId;
use App\Domain\Employees\ValueObjects\IncidentId;

interface IncidentRepositoryInterface
{
    public function findById(IncidentId $id): ?Incident;

    /**
     * @return array<Incident>
     */
    public function findByCinema(CinemaId $cinemaId, ?int $limit = null): array;

    /**
     * @return array<Incident>
     */
    public function findBySalle(SalleId $salleId): array;

    /**
     * @return array<Incident>
     */
    public function findByEmploye(EmploiId $emploiId): array;

    /**
     * @return array<Incident>
     */
    public function findByStatut(StatutIncident $statut, ?CinemaId $cinemaId = null): array;

    /**
     * @return array<Incident>
     */
    public function findBySeverite(SeveriteIncident $severite, ?CinemaId $cinemaId = null): array;

    /**
     * @return array<Incident>
     */
    public function findByType(TypeIncident $type, ?CinemaId $cinemaId = null): array;

    /**
     * @return array<Incident>
     */
    public function findOpenIncidents(?CinemaId $cinemaId = null): array;

    /**
     * @return array<Incident>
     */
    public function findCriticalIncidents(?CinemaId $cinemaId = null): array;

    /**
     * @return array<Incident>
     */
    public function findRecentIncidents(int $days = 7, ?CinemaId $cinemaId = null): array;

    public function save(Incident $incident): bool;

    public function delete(IncidentId $id): bool;

    public function countByStatut(StatutIncident $statut, ?CinemaId $cinemaId = null): int;

    /**
     * @return array<string, mixed>
     */
    public function getStatistics(CinemaId $cinemaId): array;
}
