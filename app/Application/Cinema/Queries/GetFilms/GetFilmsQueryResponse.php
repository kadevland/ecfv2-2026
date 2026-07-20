<?php

declare(strict_types=1);

namespace App\Application\Cinema\Queries\GetFilms;

use App\Application\Cinema\DTOs\FilmListItemDto;
use App\Application\Contracts\PaginatedResponseInterface;

final readonly class GetFilmsQueryResponse implements PaginatedResponseInterface
{
    /**
     * @param FilmListItemDto[] $films
     */
    public function __construct(
        public array $films,
        public int $total,
        public int $page,
        public int $perPage,
    ) {}

    /**
     * @return FilmListItemDto[]
     */
    public function getItems(): array
    {
        return $this->films;
    }

    public function getTotal(): int
    {
        return $this->total;
    }

    public function getPage(): int
    {
        return $this->page;
    }

    public function getPerPage(): int
    {
        return $this->perPage;
    }

    /**
     * @return array<string, mixed>
     */
    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'data' => array_map(fn (FilmListItemDto $film) => $film->toArray(), $this->films),
            'meta' => [
                'total'       => $this->total,
                'page'        => $this->page,
                'per_page'    => $this->perPage,
                'total_pages' => (int) ceil($this->total / $this->perPage),
            ],
        ];
    }
}
