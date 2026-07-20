<?php

declare(strict_types=1);

namespace App\Application\Public\Seance\Queries\GetSeancesByFilm;

use App\Application\Public\Seance\DTOs\SeancePublicDto;

final readonly class GetSeancesByFilmQueryResponse
{
    /**
     * @param SeancePublicDto[] $seances
     */
    public function __construct(
        public string $filmId,
        public string $filmTitre,
        public array $seances,
        public int $totalCount,
    ) {}

    /**
     * @return SeancePublicDto[]
     */
    public function getSeances(): array
    {
        return $this->seances;
    }

    public function getCount(): int
    {
        return count($this->seances);
    }

    public function isEmpty(): bool
    {
        return empty($this->seances);
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'film_id'     => $this->filmId,
            'film_titre'  => $this->filmTitre,
            'seances'     => array_map(fn (SeancePublicDto $seance) => $seance->toArray(), $this->seances),
            'total_count' => $this->totalCount,
            'count'       => $this->getCount(),
        ];
    }
}
