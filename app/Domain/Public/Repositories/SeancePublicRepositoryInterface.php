<?php

declare(strict_types=1);

namespace App\Domain\Public\Repositories;

interface SeancePublicRepositoryInterface
{
    /**
     * Trouve les séances d'un film
     *
     * @return array{seances: array<string, mixed>, film_titre: string, total_count: int}
     */
    public function findByFilmId(string $filmId, bool $futuresOnly = true, ?int $limit = null): array;

    /**
     * Trouve les séances d'un cinéma
     *
     * @return array{seances: array<string, mixed>, cinema_nom: string, total_count: int}
     */
    public function findByCinemaId(string $cinemaId, bool $futuresOnly = true, ?int $limit = null): array;

    /**
     * Trouve une séance par ID
     *
     * @return array<string, mixed>|null
     */
    public function findById(string $seanceId): ?array;

    /**
     * Trouve les séances disponibles avec filtres
     *
     * @param array<string, mixed> $filters
     * @return array{seances: array<string, mixed>, total_count: int}
     */
    public function findAvailableSeances(array $filters = [], ?int $limit = null): array;
}
