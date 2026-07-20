<?php

declare(strict_types=1);

namespace App\Application\Seance\Queries\GetSeancesList;

use App\Application\Seance\DTOs\SeanceListItemDto;
use App\Application\Contracts\PaginatedResponseInterface;

final readonly class GetSeancesListQueryResponse implements PaginatedResponseInterface
{
    /**
     * @param SeanceListItemDto[] $seances
     */
    public function __construct(
        public array $seances,
        public int $total,
        public int $page,
        public int $perPage,
    ) {}

    public function getItems(): array
    {
        return $this->seances;
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
    public function toArray(): array
    {
        return [
            'data' => array_map(fn (SeanceListItemDto $seance) => $seance->toArray(), $this->seances),
            'meta' => [
                'total'       => $this->total,
                'page'        => $this->page,
                'per_page'    => $this->perPage,
                'total_pages' => (int) ceil($this->total / $this->perPage),
            ],
        ];
    }
}
