<?php

declare(strict_types=1);

namespace App\Application\Cinema\Queries\GetCinemasList;

use App\Application\Cinema\DTOs\CinemaListItemDto;
use App\Application\Contracts\PaginatedResponseInterface;

final readonly class GetCinemasListQueryResponse implements PaginatedResponseInterface
{
    /**
     * @param CinemaListItemDto[] $cinemas
     */
    public function __construct(
        public array $cinemas,
        public int $total,
        public int $page,
        public int $perPage,
    ) {}

    /**
     * @return CinemaListItemDto[]
     */
    public function getItems(): array
    {
        return $this->cinemas;
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
            'data' => array_map(fn (CinemaListItemDto $cinema) => $cinema->toArray(), $this->cinemas),
            'meta' => [
                'total'       => $this->total,
                'page'        => $this->page,
                'per_page'    => $this->perPage,
                'total_pages' => (int) ceil($this->total / $this->perPage),
            ],
        ];
    }
}
