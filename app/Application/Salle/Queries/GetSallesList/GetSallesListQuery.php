<?php

declare(strict_types=1);

namespace App\Application\Salle\Queries\GetSallesList;

use App\Application\Contracts\QueryInterface;

final readonly class GetSallesListQuery implements QueryInterface
{
    /**
     * @param array<string>|null $technologies
     * @param array<string, mixed>|null $filters
     */
    public function __construct(
        public int $page = 1,
        public int $perPage = 10,
        public ?string $search = null,
        public ?array $technologies = null,
        public ?string $statut = null,
        public ?bool $accessibilitePmr = null,
        public ?bool $climatisation = null,
        public ?string $qualiteSon = null,
        public ?string $tailleEcran = null,
        public ?string $typeEcran = null,
        public ?string $sortBy = 'numero',
        public ?string $sortDirection = 'asc',
        public ?array $filters = null,
    ) {}

    public function isValid(): bool
    {
        return $this->page >= 1 &&
               $this->perPage >= 1 &&
               $this->perPage <= 100;
    }
}
