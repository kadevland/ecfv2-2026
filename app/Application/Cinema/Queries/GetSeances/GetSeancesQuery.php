<?php

declare(strict_types=1);

namespace App\Application\Cinema\Queries\GetSeances;

use App\Application\Contracts\QueryInterface;

final readonly class GetSeancesQuery implements QueryInterface
{
    public function __construct(
        public ?string $filmId = null,
        public ?string $salleId = null,
        public ?string $dateFrom = null,
        public ?string $dateTo = null,
        public ?string $statut = null,
        public int $page = 1,
        public int $perPage = 20,
        public string $sortBy = 'date_seance',
        public string $sortDirection = 'asc',
    ) {}

    public function isValid(): bool
    {
        return $this->page >= 1 &&
               $this->perPage >= 1 &&
               $this->perPage <= 100 &&
               in_array($this->sortDirection, ['asc', 'desc']);
    }
}
