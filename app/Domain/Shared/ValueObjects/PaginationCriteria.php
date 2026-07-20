<?php

declare(strict_types=1);

namespace App\Domain\Shared\ValueObjects;

use InvalidArgumentException;

final readonly class PaginationCriteria
{
    /**
     * @param array<string, mixed>|null $filters
     */
    public function __construct(
        public int $page,
        public int $perPage,
        public ?array $filters = null,
        public string $sortBy = 'id',
        public string $sortDirection = 'asc',
    ) {
        if ($page < 1) {
            throw new InvalidArgumentException('Page must be greater than 0');
        }

        if ($perPage < 1 || $perPage > 100) {
            throw new InvalidArgumentException('PerPage must be between 1 and 100');
        }

        if (!in_array(strtolower($sortDirection), ['asc', 'desc'])) {
            throw new InvalidArgumentException('SortDirection must be "asc" or "desc"');
        }
    }

    /**
     * Calcule l'offset pour la base de données
     */
    public function offset(): int
    {
        return ($this->page - 1) * $this->perPage;
    }

    /**
     * Retourne le nombre d'éléments à récupérer
     */
    public function limit(): int
    {
        return $this->perPage;
    }
}
