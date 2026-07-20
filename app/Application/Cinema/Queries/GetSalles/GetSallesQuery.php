<?php

declare(strict_types=1);

namespace App\Application\Cinema\Queries\GetSalles;

use App\Application\Contracts\QueryInterface;

final readonly class GetSallesQuery implements QueryInterface
{
    public function __construct(
        public ?string $cinemaId = null,
        public ?string $statut = null,
        /** @var array<string>|null */ public ?array $technologies = null,
        public ?bool $accessibilitePmr = null,
        public int $page = 1,
        public int $perPage = 20,
        public string $sortBy = 'nom',
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
