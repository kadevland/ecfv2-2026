<?php

declare(strict_types=1);

namespace App\Application\Public\Film\Queries\GetFilmsCatalog;

final readonly class GetFilmsCatalogQueryResponse
{
    /**
     * @param array<int, array<string, mixed>> $films
     * @param array<string, mixed> $filters
     */
    public function __construct(
        public array $films,
        public int $total,
        public int $page,
        public int $perPage,
        public int $totalPages,
        public array $filters,
    ) {}
}
