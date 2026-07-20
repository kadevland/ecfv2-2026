<?php

declare(strict_types=1);

namespace App\Application\Cinema\Queries\GetSalles;

use App\Application\Cinema\DTOs\SalleListItemDto;
use App\Application\Contracts\PaginatedResponseInterface;

final readonly class GetSallesQueryResponse implements PaginatedResponseInterface
{
    /**
     * @param SalleListItemDto[] $salles
     */
    public function __construct(
        public array $salles,
        public int $total,
        public int $page,
        public int $perPage,
    ) {}

    /**
     * @return SalleListItemDto[]
     */
    public function getItems(): array
    {
        return $this->salles;
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
            'data' => array_map(fn (SalleListItemDto $salle) => $salle->toArray(), $this->salles),
            'meta' => [
                'total'       => $this->total,
                'page'        => $this->page,
                'per_page'    => $this->perPage,
                'total_pages' => (int) ceil($this->total / $this->perPage),
            ],
        ];
    }
}
