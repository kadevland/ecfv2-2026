<?php

declare(strict_types=1);

namespace App\Domain\Cinema\Repositories;

use DateTimeInterface;
use App\Domain\Cinema\Entities\Film;
use App\Domain\Cinema\ValueObjects\FilmId;
use App\Domain\Shared\ValueObjects\PaginationCriteria;
use App\Domain\Shared\ValueObjects\PaginatedCollection;

interface FilmRepositoryInterface
{
    /**
     * Save film entity (create or update)
     */
    public function save(Film $film): bool;

    /**
     * Find film by ID
     */
    public function findById(FilmId $id): ?Film;

    /**
     * Find films by multiple IDs (batch loading)
     *
     * @param array<FilmId> $ids
     * @return array<string, Film> Indexed by FilmId value for O(1) access
     */
    public function findByIds(array $ids): array;

    /**
     * Find all active films (currently in theaters)
     *
     * @return array<Film>
     */
    public function findAllActive(): array;

    /**
     * Find films currently in theaters
     *
     * @return array<Film>
     */
    public function findInTheaters(): array;

    /**
     * Find films by title
     *
     * @return array<Film>
     */
    public function findByTitle(string $title): array;

    /**
     * Find films by director
     *
     * @return array<Film>
     */
    public function findByDirector(string $director): array;

    /**
     * Find films by genre
     *
     * @return array<Film>
     */
    public function findByGenre(string $genre): array;

    /**
     * Find films by classification
     *
     * @return array<Film>
     */
    public function findByClassification(string $classification): array;

    /**
     * Find upcoming films (not yet released)
     *
     * @return array<Film>
     */
    public function findUpcoming(): array;

    /**
     * Find films released in date range
     *
     * @return array<Film>
     */
    public function findReleasedBetween(DateTimeInterface $startDate, DateTimeInterface $endDate): array;

    /**
     * Find top rated films
     *
     * @return array<Film>
     */
    public function findTopRated(int $limit = 10): array;

    /**
     * Find films by minimum rating
     *
     * @return array<Film>
     */
    public function findByMinimumRating(float $minRating): array;

    /**
     * Search films by text (title, director, actors)
     *
     * @return array<Film>
     */
    public function searchByText(string $searchText): array;

    /**
     * Find films by duration range
     *
     * @return array<Film>
     */
    public function findByDurationRange(int $minMinutes, int $maxMinutes): array;

    /**
     * Find films by language
     *
     * @return array<Film>
     */
    public function findByLanguage(string $language): array;

    /**
     * Delete film
     */
    public function delete(FilmId $id): bool;

    /**
     * Check if film exists
     */
    public function exists(FilmId $id): bool;

    /**
     * Find films with pagination
     */
    public function findWithPagination(PaginationCriteria $criteria): PaginatedCollection;

    /**
     * Get total count of active films
     */
    public function countActive(): int;

    /**
     * Get total count of films in theaters
     */
    public function countInTheaters(): int;

    /**
     * Generate next identity for new film
     */
    public function nextIdentity(): FilmId;

    /**
     * Update film rating with new review
     */
    public function updateRating(FilmId $id, float $newRating): bool;

    /**
     * Find films ending exploitation soon
     *
     * @return array<Film>
     */
    public function findEndingSoon(int $days = 7): array;

    /**
     * Find most popular films (by number of reviews)
     *
     * @return array<Film>
     */
    public function findMostPopular(int $limit = 10): array;

    /**
     * Find films with poster
     *
     * @return array<Film>
     */
    public function findWithPoster(): array;

    /**
     * Find films with trailer
     *
     * @return array<Film>
     */
    public function findWithTrailer(): array;

    /**
     * Find recent films (released in last N days)
     *
     * @return array<Film>
     */
    public function findRecent(int $days = 30): array;

    /**
     * Get formatted films data for select dropdowns
     *
     * @return array<array{id: string, titre: string, annee_sortie: int, duree_minutes: int, classification_age: string, display_name: string}>
     */
    public function findAllForSelect(): array;
}
