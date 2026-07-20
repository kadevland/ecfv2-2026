<?php

declare(strict_types=1);

namespace App\Domain\Cinema\Repositories;

use App\Domain\Cinema\Entities\Cinema;
use App\Domain\Cinema\ValueObjects\CinemaId;
use App\Domain\Shared\ValueObjects\PaginationCriteria;
use App\Domain\Shared\ValueObjects\PaginatedCollection;

interface CinemaRepositoryInterface
{
    /**
     * Save cinema entity (create or update)
     */
    public function save(Cinema $cinema): bool;

    /**
     * Find cinema by ID
     */
    public function findById(CinemaId $id): ?Cinema;

    /**
     * Find cinemas by multiple IDs (batch loading)
     *
     * @param array<CinemaId> $ids
     * @return array<string, Cinema> Indexed by CinemaId value for O(1) access
     */
    public function findByIds(array $ids): array;

    /**
     * Find all active cinemas
     *
     * @return array<Cinema>
     */
    public function findAllActive(): array;

    /**
     * Find cinema by name
     */
    public function findByNom(string $nom): ?Cinema;

    /**
     * Find cinemas by city
     *
     * @return array<Cinema>
     */
    public function findByVille(string $ville): array;

    /**
     * Delete cinema
     */
    public function delete(CinemaId $id): bool;

    /**
     * Check if cinema exists
     */
    public function exists(CinemaId $id): bool;

    /**
     * Find cinemas with pagination
     */
    public function findWithPagination(PaginationCriteria $criteria): PaginatedCollection;

    /**
     * Search cinemas by location (city, address)
     *
     * @return array<Cinema>
     */
    public function searchByLocation(string $location): array;

    /**
     * Find cinemas by region (multiple cities)
     *
     * @param array<string> $cities
     * @return array<Cinema>
     */
    public function findByRegion(array $cities): array;

    /**
     * Get total count of active cinemas
     */
    public function countActive(): int;

    /**
     * Generate next identity for new cinema
     */
    public function nextIdentity(): CinemaId;

    /**
     * Find cinemas that have GPS coordinates
     *
     * @return array<Cinema>
     */
    public function findWithGpsCoordinates(): array;

    /**
     * Find cinemas in France métropolitaine
     *
     * @return array<Cinema>
     */
    public function findInFranceMetropolitaine(): array;

    /**
     * Find cinemas in Belgium
     *
     * @return array<Cinema>
     */
    public function findInBelgique(): array;

    /**
     * Find cinemas within a geographic area
     *
     * @return array<Cinema>
     */
    public function findInGeographicArea(float $minLat, float $maxLat, float $minLng, float $maxLng): array;

    /**
     * Get formatted cinemas data for select dropdowns
     *
     * @return array<array{id: string, nom: string, ville: string, pays: string, display_name: string}>
     */
    public function findAllForSelect(): array;
}
